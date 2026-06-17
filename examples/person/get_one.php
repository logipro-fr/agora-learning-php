<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$personUuid = 'per_01XXXXXXXXXXXXXXXXXXXXXXXXX';

try {
    $person = $client->getPerson($personUuid);
    var_dump($person);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
