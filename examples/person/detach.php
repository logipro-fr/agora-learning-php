<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

try {
    // Détache le contact de l'application source (il reste sur Agora, désignable par son UUID) ;
    // un contact inconnu lève une AgoraLearningClientException (code 404)
    $client->detachPerson(new ExternalId('MonERP', 'P-42'));
    echo 'Contact détaché' . PHP_EOL;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
