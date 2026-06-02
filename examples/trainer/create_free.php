<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\Enum\Gender;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$trainerInput = new TrainerFreeInput(
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
    'Formateur freelance en management',
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
    $trainer = $client->createTrainerFree($trainerInput);
    var_dump($trainer);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
