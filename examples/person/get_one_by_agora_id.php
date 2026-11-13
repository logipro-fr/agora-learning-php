<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\AgoraId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$identifier = new AgoraId('per_01a1305e04fb758bb919b9c3e3f3a022');

try {
    $person = $client->getPerson($identifier);
    var_dump($person);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
