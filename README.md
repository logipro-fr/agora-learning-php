![PHP](https://img.shields.io/badge/PHP-%5E7.4%20%7C%7C%20%5E8.0-blue)
![Licence](https://img.shields.io/badge/licence-MIT-green)
![Version](https://img.shields.io/badge/version-1.0.0-orange)

# Agora Learning - Client PHP

Client PHP officiel pour l'API **Agora Learning**. Il permet à des applications tierces de gérer des ressources de formation (personnes, apprenants, formateurs, sociétés, sessions, inscriptions) via une interface orientée objet, sans avoir à manipuler directement les requêtes HTTP ou les jetons d'authentification.

---

## Table des matières

- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Utilisation](docs/usage.md)
    - [Vérification de connexion (ping)](#quick-start)
    - [Gestion des erreurs](docs/usage.md#gestion-des-erreurs)
- [Ressources disponibles](#ressources-disponibles)

---

## Installation

```bash
composer require logipro/agora-learning-php
```

Voir [docs/installation.md](docs/installation.md) pour les prérequis détaillés.

---

## Quick Start

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;

$client = new AgoraLearningClient(
    'https://votre-instance.example.com', // TODO: à adapter
    'votre_cle_api'                       // TODO: à adapter
);

// Vérifier que l'API est joignable
$ping = $client->ping();
if ($ping->getStatusCode() !== 200) {
    throw new \RuntimeException(
        'Impossible de joindre l\'API Agora (HTTP ' . $ping->getStatusCode() . ')'
    );
}

// Créer un apprenant
$learner = $client->createLearner(new LearnerInput(
    'Dupont',
    'Marie',
    'marie.dupont@example.com'
));

echo $learner->uuid;
```

---

## Ressources disponibles

| Ressource      | Méthodes disponibles                                                                                                                                            |
| -------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Auth**       | `ping()`                                                                                                                                                        |
| **Person**     | `getPerson()`, `getCollectionPerson()`, `createPerson()`                                                                                                        |
| **Trainer**    | `getTrainer()`, `getCollectionTrainer()`, `createTrainerEmployee()`, `createTrainerFree()`                                                                      |
| **Learner**    | `getLearner()`, `getCollectionLearner()`, `createLearner()`                                                                                                     |
| **Society**    | `getSociety()`, `getCollectionSociety()`, `createSociety()`                                                                                                     |
| **Session**    | `getSession()`, `getCollectionSession()`, `createSession()`                                                                                                     |
| **Enrollment** | `getEnrollment()`, `getCollectionEnrollmentFromSession()`, `getCollectionEnrollmentFromLearner()`, `getEnrollmentFromSessionAndLearner()`, `createEnrollment()` |

Consultez [docs/usage.md](docs/usage.md) pour un exemple complet par méthode.

---

## Gestion des erreurs

Voir la section dédiée dans [docs/usage.md#gestion-des-erreurs](docs/usage.md#gestion-des-erreurs).

---

## Développement

### Prérequis

PHP 7.4+ et Composer installés localement (ou via Docker).

### Installer les dépendances de développement

```bash
composer install
```

### Outils disponibles

| Commande            | Outil            | Rôle                                               |
| ------------------- | ---------------- | -------------------------------------------------- |
| `composer cs-check` | php-cs-fixer     | Vérifie le style PSR-12 sans modifier les fichiers |
| `composer cs-fix`   | php-cs-fixer     | Corrige automatiquement les violations de style    |
| `composer phpstan`  | PHPStan niveau 6 | Analyse statique du répertoire `src/`              |
| `composer test`     | PHPUnit 9        | Lance la suite de tests unitaires                  |
| `composer qa`       | -                | Enchaîne les trois commandes ci-dessus             |

### Lancer tous les contrôles qualité

```bash
composer qa
```

### Style de code

La configuration est dans [`.php-cs-fixer.php`](.php-cs-fixer.php) :

- Ruleset **@PSR12**
- `declare(strict_types=1)` obligatoire
- Imports triés alphabétiquement, imports inutilisés supprimés
- Cache dans `.php-cs-fixer.cache` (ignoré par git)

```bash
# Voir les fichiers à corriger sans les toucher
composer cs-check

# Appliquer les corrections
composer cs-fix
```

### Analyse statique

La configuration est dans [`phpstan.neon`](phpstan.neon) - niveau 6, cible `src/`.

```bash
composer phpstan
```

### Tests

La configuration est dans [`phpunit.xml.dist`](phpunit.xml.dist). La suite `Unit` pointe sur `tests/unit/`.

```bash
composer test
```
