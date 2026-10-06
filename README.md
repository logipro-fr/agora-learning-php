![PHP](https://img.shields.io/badge/PHP-%5E7.4%20%7C%7C%20%5E8.0-blue)
![Licence](https://img.shields.io/badge/licence-MIT-green)
![Version](https://img.shields.io/badge/version-0.0.2-orange)

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
| **Session**    | `getSession()` (par `AgoraId` ou `ExternalId`), `getCollectionSession()`, `createSession()`                                                                     |
| **Enrollment** | `getEnrollment()`, `getCollectionEnrollmentFromSession()`, `getCollectionEnrollmentFromLearner()`, `getEnrollmentFromSessionAndLearner()`, `createEnrollment()` |

Consultez [docs/usage.md](docs/usage.md) pour un exemple complet par méthode.

---

## Exemples exécutables

Le dossier [`examples/`](examples/) contient un fichier PHP autonome par méthode, prêt à être lancé en ligne de commande.

### Configuration

```bash
cp examples/config.php.dist examples/config.php
# Éditer examples/config.php et renseigner l'URL et la clé API
```

> `examples/config.php` est ignoré par git - ne pas le versionner.

### Lancer un exemple

```bash
php examples/ping.php
php examples/person/get_collection.php
php examples/society/create.php
# etc.
```

### Arborescence

```
examples/
├── config.php.dist          ← modèle à copier
├── ping.php
├── person/
│   ├── get_collection.php
│   ├── get_one.php
│   └── create.php
├── trainer/
│   ├── get_collection.php
│   ├── get_one.php
│   ├── create_employee.php
│   └── create_free.php
├── learner/
│   ├── get_collection.php
│   ├── get_one.php
│   └── create.php
├── society/
│   ├── get_collection.php
│   ├── get_one.php
│   └── create.php
├── session/
│   ├── get_collection.php
│   ├── get_one.php
│   ├── get_one_by_agora_id.php
│   ├── get_one_by_external_id.php
│   ├── create_fixed.php
│   └── create_opened.php
└── enrollment/
    ├── get_from_session.php
    ├── get_from_learner.php
    ├── get_one.php
    ├── get_from_session_and_learner.php
    └── create.php
```

---

## Gestion des erreurs

Voir la section dédiée dans [docs/usage.md#gestion-des-erreurs](docs/usage.md#gestion-des-erreurs).

---

## Développement

### Prérequis

PHP 7.4+ et Composer installés localement, **ou** Docker (aucun prérequis PHP local dans ce cas).

### Outils disponibles

| Commande             | Outil             | Rôle                                               |
| -------------------- | ----------------- | -------------------------------------------------- |
| `composer cs-check`  | php-cs-fixer      | Vérifie le style PSR-12 sans modifier les fichiers |
| `composer cs-fix`    | php-cs-fixer      | Corrige automatiquement les violations de style    |
| `composer phpstan`   | PHPStan niveau 10 | Analyse statique du répertoire `src/`              |
| `composer test`      | PHPUnit 9         | Lance tous les tests (unitaires + intégration)     |
| `composer test-unitaire` | PHPUnit 9     | Lance les tests unitaires (`tests/unit/`)          |
| `composer test-integration` | PHPUnit 9  | Lance les tests d'intégration (`tests/integration/`) contre une vraie instance Agora |
| `composer infection` | Infection 0.29    | Tests de mutation (suite `Unit`, 4 threads)        |
| `composer qa`        | -                 | Enchaîne cs-check, phpstan et test-unitaire        |

---

### Sans Docker

```bash
# Installer les dépendances
composer install

# Tous les contrôles qualité
composer qa

# Tous les tests (unitaires + intégration)
composer test

# Tests unitaires seuls
composer test-unitaire

# Tests d'intégration (instance Agora configurée dans .env.local)
composer test-integration

# Tests de mutation
composer infection
```

---

### Avec Docker

Trois services sont disponibles : `php` (PHP 8.2, par défaut, dépendances dans `vendor/`), `php85` (PHP 8.5, dépendances dans `vendor85/`) et `php74` (PHP 7.4, dépendances dans `vendor74/`).

#### Construire les images

```bash
# Toutes les images en une commande
docker compose build

# Une image spécifique
# PHP 8.2 (par défaut)
docker compose build php

# PHP 8.5
docker compose build php85

# PHP 7.4
docker compose build php74
```

#### Accéder au docker

```bash
docker exec -ti agora-learning-php-php-1 /bin/bash
```

#### Installer les dépendances

```bash
# PHP 8.2 (par défaut), dans vendor/
docker compose run --rm php composer update

# PHP 8.5, dans vendor85/
docker compose run --rm php85 composer update

# PHP 7.4, dans vendor74/
docker compose run --rm php74 composer update
```

#### Contrôles qualité

```bash
# PHP 8.2 (par défaut)
docker compose run --rm php composer qa

# PHP 8.5
docker compose run --rm php85 composer qa

# PHP 7.4
docker compose run --rm php74 composer qa
```

#### Tests unitaires

```bash
# PHP 8.2 (par défaut)
docker compose run --rm php composer test-unitaire

# PHP 8.5
docker compose run --rm php85 composer test-unitaire

# PHP 7.4
docker compose run --rm php74 composer test-unitaire
```

#### Tests d'intégration

Ils appellent une vraie instance Agora et y créent des données : utilisez une instance de développement, jamais la production.

La configuration se fait par deux variables :

| Variable | Rôle | Où la définir |
| --- | --- | --- |
| `AGORA_BASE_URL` | URL racine de l'instance, vue depuis le conteneur (sans `/api/external/v1`) | valeur par défaut dans `.env`, à surcharger dans `.env.local` |
| `AGORA_API_KEY` | clé API externe de l'instance | `.env.local` uniquement |

`.env.local` n'est pas versionné. `composer install` et `composer update` le créent à partir de `.env.local.dist` s'il n'existe pas. Ordre de priorité : variable d'environnement, puis `.env.local`, puis `.env`.

Si une variable est vide ou si l'instance n'est pas joignable, les tests sont ignorés (*skipped*) avec le motif.

```bash
docker compose run --rm php composer test-integration
```

#### Style de code

```bash
# Vérifier sans modifier
docker compose run --rm php composer cs-check

# Appliquer les corrections
docker compose run --rm php composer cs-fix
```

#### Analyse statique

```bash
docker compose run --rm php composer phpstan
```

#### Tests de mutation (Infection)

```bash
# PHP 8.2 (par défaut)
docker compose run --rm php composer infection

# PHP 8.5
docker compose run --rm php85 composer infection

# PHP 7.4
docker compose run --rm php74 composer infection
```

---

### Tests de mutation — Infection

La configuration est dans [`infection.json5`](infection.json5) :

- Cible : répertoire `src/`
- Framework : PHPUnit, suite `Unit`
- Mutateurs : ensemble `@default`
- Logs générés dans `var/infection/` :
    - `infection.log` — détail textuel
    - `infection.html` — rapport HTML interactif
    - `summary.log` — résumé MSI

```bash
# Lancer les tests de mutation (4 threads)
composer infection

# Avec un seuil MSI minimal (ex : 80 %)
vendor/bin/infection --threads=4 --min-msi=80 --min-covered-msi=80

# Cibler un seul fichier source
vendor/bin/infection --threads=4 --filter=src/Service/Learner/Learner.php
```

---

### Style de code

La configuration est dans [`.php-cs-fixer.php`](.php-cs-fixer.php) :

- Ruleset **@PSR12**
- `declare(strict_types=1)` obligatoire
- Imports triés alphabétiquement, imports inutilisés supprimés
- Cache dans `.php-cs-fixer.cache` (ignoré par git)

### Analyse statique

La configuration est dans [`phpstan.neon`](phpstan.neon) — niveau , cible `src/`.

### Tests unitaires

La configuration est dans [`phpunit.xml.dist`](phpunit.xml.dist). La suite `Unit` pointe sur `tests/unit/`.
