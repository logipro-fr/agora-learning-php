<?php

declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/config.php';

use AgoraLearningPhp\AgoraLearningClient;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

try {
    $response = $client->ping();
    $statusCode = $response->getStatusCode();

    if ($statusCode !== 200) {
        echo 'Impossible de joindre l\'API Phoenix (HTTP ' . $statusCode . ')' . PHP_EOL;
        echo $response->getContent(true) . PHP_EOL;
        exit(1);
    }

    echo "Connexion à l'API OK" . PHP_EOL;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
