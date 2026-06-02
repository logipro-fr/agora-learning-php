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
