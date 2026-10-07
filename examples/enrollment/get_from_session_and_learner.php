<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\AgoraId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$sessionUuid = 'ses_01XXXXXXXXXXXXXXXXXXXXXXXXX';
$learnerUuid = 'per_01XXXXXXXXXXXXXXXXXXXXXXXXX';

try {
    $enrollment = $client->getEnrollmentFromSessionAndLearner(new AgoraId($sessionUuid), new AgoraId($learnerUuid));
    var_dump($enrollment);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
