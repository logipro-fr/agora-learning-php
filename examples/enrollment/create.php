<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$enrollmentInput = new EnrollmentInput(
    'ses_01XXXXXXXXXXXXXXXXXXXXXXXXX',
    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
    true
);

try {
    $enrollment = $client->createEnrollment($enrollmentInput);
    var_dump($enrollment);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
