# Configuration

## Instanciation du client

La classe principale est `AgoraLearningPhp\AgoraLearningClient`. Elle prend deux paramètres obligatoires :

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use AgoraLearningPhp\AgoraLearningClient;

$client = new AgoraLearningClient(
    'https://votre-instance.example.com', // URL de base de l'API
    'votre_cle_api'                      // clé API fournie par l'administrateur
);
```

## Paramètres du constructeur

| Paramètre | Type     | Description                                                                     |
| --------- | -------- | ------------------------------------------------------------------------------- |
| `$url`    | `string` | URL de base de l'instance Agora Learning. Exemple : `https://agora.example.com` |
| `$apiKey` | `string` | Clé API en clair, fournie par l'administrateur de la plateforme                 |

## Mécanisme d'authentification

L'authentification se déroule en deux étapes, entièrement gérées de façon transparente par le client.

### Étape 1 — Échange de la clé API contre un jeton Bearer

Lors du premier appel à n'importe quelle méthode, le client envoie automatiquement une requête :

```
POST /api/external/v1/auth/token
Body : { "api_key": "votre_cle_api" }
```

L'API retourne un objet JSON contenant :

```json
{
    "token": "eyJ0eXAiOiJKV1Qi...",
    "expires_in": 3600
}
```

### Étape 2 — Utilisation du jeton Bearer

Toutes les requêtes suivantes utilisent automatiquement le jeton dans l'en-tête HTTP :

```
Authorization: Bearer eyJ0eXAiOiJKV1Qi...
```

### Rafraîchissement automatique du jeton

Le client gère le cycle de vie du jeton via des propriétés statiques de classe :

- Le jeton est mis en cache après le premier appel
- Il est considéré expiré 60 secondes avant sa date d'expiration réelle (marge de sécurité)
- Un nouveau jeton est demandé automatiquement si le jeton est absent ou expiré
- En cas d'échec d'authentification (HTTP ≠ 200), une `\RuntimeException` est levée

**L'intégrateur n'a rien à gérer** : l'ensemble de ce cycle est transparent.

## URL des endpoints

Les URLs sont construites à partir de la base URL fournie en suivant ce schéma :

```
{baseUrl}/api/external/v1/{ressource}
```

Exemples :

| Ressource                                 | Endpoint                                                       |
| ----------------------------------------- | -------------------------------------------------------------- |
| Token                                     | `/api/external/v1/auth/token`                                  |
| Ping                                      | `/api/external/v1/ping`                                        |
| Personnes                                 | `/api/external/v1/persons`                                     |
| Formateurs (salarié)                      | `/api/external/v1/trainers/employee`                           |
| Formateurs (indépendant)                  | `/api/external/v1/trainers/free`                               |
| Formateurs (lecture)                      | `/api/external/v1/trainers`                                    |
| Apprenants                                | `/api/external/v1/learners`                                    |
| Sociétés                                  | `/api/external/v1/societies`                                   |
| Sessions fixes                            | `/api/external/v1/sessions/fixed`                              |
| Sessions ouvertes                         | `/api/external/v1/sessions/opened`                             |
| Inscriptions                              | `/api/external/v1/enrollments`                                 |
| Inscriptions d'une session                | `/api/external/v1/sessions/{uuid}/enrollments`                 |
| Inscriptions d'un apprenant               | `/api/external/v1/learners/{uuid}/enrollments`                 |
| Inscription d'un couple session+apprenant | `/api/external/v1/sessions/{uuid}/learners/{uuid}/enrollments` |

## Exemple complet d'initialisation

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use AgoraLearningPhp\AgoraLearningClient;

// Initialisation du client
$client = new AgoraLearningClient(
    'https://agora.example.com', // TODO: à adapter
    'a1b2c3d4e5f6...'            // TODO: à adapter
);

// Test de connectivité (optionnel mais recommandé au démarrage)
$ping = $client->ping();
if ($ping->getStatusCode() !== 200) {
    throw new \RuntimeException(
        'Impossible de joindre l\'API Agora (HTTP ' . $ping->getStatusCode() . ')'
    );
}

// Le client est prêt à être utilisé
```

## Gestion des erreurs d'authentification

Si la clé API est invalide ou révoquée, la méthode `requestToken()` lève une exception :

```php
try {
    $client = new AgoraLearningClient('https://agora.example.com/phoenix', 'cle_invalide');
    $persons = $client->getCollectionPerson(); // déclenche l'échange du jeton
} catch (\RuntimeException $e) {
    // "Authentication failed (HTTP 401): unable to retrieve token."
    echo $e->getMessage();
}
```
