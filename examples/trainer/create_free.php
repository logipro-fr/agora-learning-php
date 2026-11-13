<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$trainerInput = new TrainerFreeInput(
    new ExternalId('MonERP', uniqid('T-')),
    'Bernard',
    'Pierre',
    'pierre.bernard@example.com',
    true,
    true,
    Gender::GENDER_MALE,
    '0123456789',
    '0612345678',
    '5 rue des Indépendants',
    '33000',
    'Bordeaux',
    'France',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/homme.jpg',
    new \DateTimeImmutable('1982-11-08'),
    'Formateur indépendant',
    null,
    'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf',
    null,
    'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf',
    80.0,
    600.0,
    '12345678901234',
    'FR12345678901',
    '987654321',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf',
    '5 rue des Indépendants',
    '33000',
    'Bordeaux',
    'France'
);

try {
    $created = $client->createTrainerFree($trainerInput);
    var_dump($created);
    var_dump($client->getTrainer($created->agoraId));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
