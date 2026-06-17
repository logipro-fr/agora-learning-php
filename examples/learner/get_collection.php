<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

try {
    $learners = $client->getCollectionLearner();

    foreach ($learners as $learner) {
        var_dump($learner);
    }
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
