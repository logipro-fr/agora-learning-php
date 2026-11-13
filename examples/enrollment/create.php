<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$enrollmentInput = new EnrollmentInput(
    new ExternalId('MonERP', uniqid('E-')),
    'ses_01XXXXXXXXXXXXXXXXXXXXXXXXX',
    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
    true
);

try {
    $created = $client->createEnrollment($enrollmentInput);
    var_dump($created);
    var_dump($client->getEnrollment($created->agoraId));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
