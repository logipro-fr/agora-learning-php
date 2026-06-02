<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\Enum\Gender;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$trainerInput = new TrainerEmployeeInput(
    'Martin',
    'Sophie',
    'sophie.martin@example.com',
    true,
    true,
    Gender::GENDER_FEMALE,
    '0123456789',
    '0612345678',
    '12 avenue des Formateurs',
    '69001',
    'Lyon',
    'France',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/femme.jpg',
    new \DateTimeImmutable('1990-03-20'),
    'Formatrice senior',
    'soc_01XXXXXXXXXXXXXXXXXXXXXXXXX',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf'
);

try {
    $trainer = $client->createTrainerEmployee($trainerInput);
    var_dump($trainer);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
