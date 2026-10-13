<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\AgoraId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$sessionUuid = 'ses_01M4R73VMPFMPS3SX4YTP17XRJ';
$learnerUuid = 'per_01M4R6GRADFF59XSF523S4GVY8';

try {
    $enrollment = $client->getEnrollmentFromSessionAndLearner(new AgoraId($sessionUuid), new AgoraId($learnerUuid));
    var_dump($enrollment);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
