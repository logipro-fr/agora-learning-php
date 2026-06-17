<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

try {
    $sessions = $client->getCollectionSession();

    foreach ($sessions as $session) {
        var_dump($session);
    }
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
