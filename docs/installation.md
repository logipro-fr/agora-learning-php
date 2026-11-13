# Installation

## Prérequis

- **PHP** 7.4 ou supérieur
- Extension PHP **curl** activée (utilisée par le client HTTP Symfony)
- Extension PHP **json** activée
- [Composer](https://getcomposer.org/) installé

## Installation via Composer

```bash
composer require logipro/agora-learning-php
```

## Dépendances installées automatiquement

Le paquet installe les dépendances suivantes via Composer :

| Dépendance                | Versions compatibles       |
| ------------------------- | -------------------------- |
| `symfony/http-client`     | ^5.4, ^6.4, ^7.0, ^8.0    |
| `symfony/http-foundation` | ^5.4, ^6.4, ^7.0, ^8.0    |
| `symfony/mime`            | ^5.4, ^6.4, ^7.0, ^8.0    |

## Compatibilité Symfony

| Version Symfony | Supportée |
| --------------- | --------- |
| 5.4 (LTS)       | Oui       |
| 6.4 (LTS)       | Oui       |
| 7.x             | Oui       |
| 8.x             | Oui       |

Le client peut être intégré dans n'importe quel projet PHP, avec ou sans framework Symfony.

## Installer en PHP 7 ou en PHP 8

### Dans un projet qui utilise la bibliothèque

La commande est la même pour PHP 7.4 et pour PHP 8.x :

```bash
composer require logipro/agora-learning-php
```

Composer choisit les versions de Symfony compatibles avec le PHP du projet :

| PHP du projet | Version de Symfony installée |
| ------------- | ---------------------------- |
| 7.4, 8.0      | 5.4                          |
| 8.1           | 6.4                          |
| 8.2, 8.3      | 7.x                          |
| 8.4 et plus   | 8.x                          |

Composer se base sur le PHP qui lance la commande. Si ce PHP n'est pas celui de la production (par exemple PHP 8.3 sur le poste et PHP 7.4 sur le serveur), fixez la version cible dans le `composer.json` **de votre projet**, puis relancez la résolution :

```bash
composer config platform.php 7.4.33
composer update
```

Seul le `config.platform` du projet compte : celui d'une dépendance est ignoré par Composer.

#### Sans PHP local : utiliser les images Docker de la bibliothèque

Les images Docker de ce dépôt contiennent PHP et Composer. Elles peuvent servir à installer la bibliothèque dans un autre projet. Construisez-les une fois, depuis le dépôt de la bibliothèque :

```bash
docker compose build
```

Ensuite, lancez la commande depuis le répertoire de **votre projet** :

```bash
# PHP 8.2 (par défaut)
docker run --rm -u "$(id -u):$(id -g)" -e COMPOSER_HOME=/tmp/composer \
    -v "$PWD":/app -w /app agora-learning-php-php \
    composer require logipro/agora-learning-php

# PHP 8.5 : remplacer l'image par agora-learning-php-php85
# PHP 7.4 : remplacer l'image par agora-learning-php-php74
```

- `-u "$(id -u):$(id -g)"` fait appartenir `vendor/` et `composer.lock` à votre utilisateur, et non à root.
- `-e COMPOSER_HOME=/tmp/composer` donne à Composer un répertoire de cache accessible en écriture.
- Le nom de l'image vient du nom du répertoire de la bibliothèque (`agora-learning-php`). Si vous l'avez cloné ailleurs, vérifiez le nom avec `docker images | grep php`.

### Pour développer la bibliothèque

Trois conteneurs Docker sont fournis :

| Service | PHP | Répertoire des dépendances |
| ------- | --- | -------------------------- |
| `php`   | 8.2 (par défaut) | `vendor/`   |
| `php85` | 8.5              | `vendor85/` |
| `php74` | 7.4              | `vendor74/` |

Les conteneurs `php85` et `php74` montent `vendor85/` et `vendor74/` à la place de `vendor/`. Chaque version a donc ses propres dépendances : Symfony 7 en PHP 8.2, Symfony 8 en PHP 8.5, Symfony 5.4 en PHP 7.4. On peut passer de l'une à l'autre sans réinstaller.

```bash
# PHP 8.2 (par défaut)
docker compose run --rm php composer update
docker compose run --rm php composer qa

# PHP 8.5
docker compose run --rm php85 composer update
docker compose run --rm php85 composer qa

# PHP 7.4
docker compose run --rm php74 composer update
docker compose run --rm php74 composer qa
```

Au premier `composer install` ou `composer update`, Composer crée `.env.local` à partir de `.env.local.dist`. Ce fichier n'est pas versionné : renseignez-y `AGORA_API_KEY` (et `AGORA_BASE_URL` si l'URL par défaut de `.env` ne convient pas) pour lancer les tests d'intégration. Ce script ne s'exécute que dans le dépôt de la bibliothèque, pas dans les projets qui l'installent.

Utilisez `composer update` plutôt que `composer install`. Le `composer.lock` n'est pas versionné. Il est partagé entre les conteneurs et contient la résolution du dernier conteneur qui a lancé `composer update`. Un `composer install` dans un autre conteneur installerait des versions résolues pour un autre PHP.

## Vérification de l'installation

Après installation, vérifiez que l'autoload est fonctionnel :

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use AgoraLearningPhp\AgoraLearningClient;

$client = new AgoraLearningClient(
    'https://votre-instance.example.com', // à adapter
    'votre_cle_api'                       // à adapter
);

$ping = $client->ping();
if ($ping->getStatusCode() !== 200) {
    throw new \RuntimeException(
        'Impossible de joindre l\'API Agora (HTTP ' . $ping->getStatusCode() . ')'
    );
}
```
