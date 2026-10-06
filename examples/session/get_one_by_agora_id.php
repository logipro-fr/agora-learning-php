<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\AgoraId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$identifier = new AgoraId('ses_01a1108ab6187f16b82c84b0af1564d9');

try {
    $session = $client->getSession($identifier);
    var_dump($session);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
