<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$enrollmentUuid = 'enr_01XXXXXXXXXXXXXXXXXXXXXXXXX';

try {
    $enrollment = $client->getEnrollment($enrollmentUuid);
    var_dump($enrollment);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
