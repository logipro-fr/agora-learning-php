<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$learnerInput = new LearnerInput(
    new ExternalId('MonERP', uniqid('L-')),
    'Leblanc',
    'Alice',
    'alice.leblanc.recovery@example.com',
    'alice.leblanc@example.com',
    Gender::GENDER_FEMALE,
    '0123456789',
    '8 rue des Apprenants',
    '13001',
    'Marseille',
    'France',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/femme.jpg',
    new \DateTimeImmutable('1995-07-22'),
    'Chargée de projet'
);

try {
    $created = $client->createLearner($learnerInput);
    var_dump($created);
    var_dump($client->getLearner($created->agoraId));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
