<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\AgoraId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$societyUuid = 'soc_01XXXXXXXXXXXXXXXXXXXXXXXXX';

try {
    $society = $client->getSociety(new AgoraId($societyUuid));
    var_dump($society);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
