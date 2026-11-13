<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

try {
    // Détache la session de l'application source (elle reste sur Agora, désignable par son UUID) ;
    // une session inconnue lève une AgoraLearningClientException (code 404)
    $client->detachSession(new ExternalId('MonERP', 'S-42'));
    echo 'Session détachée' . PHP_EOL;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
