<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\Enum\Gender;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$legalPerson = new PersonInput(
    'Durand',
    'Paul',
    'paul.durand@example.com',
    Gender::GENDER_MALE,
    '0123456789'
);

$administrativePerson = new PersonInput(
    'Girard',
    'Marie',
    'marie.girard@example.com',
    Gender::GENDER_FEMALE,
    '0987654321'
);

$societyInput = new SocietyInput(
    'Formation Excellence SAS',
    '12345678901234',
    'SAS',
    'contact@formation-excellence.fr',
    '0145678901',
    '15 rue de la Formation',
    '75008',
    'Paris',
    'France',
    '15 rue de la Formation',
    '75008',
    'Paris',
    'France',
    $legalPerson,
    $administrativePerson,
    null,
    'https://www.agora-learning.com/apiAgoraLearning/ressources/logo.png'
);

try {
    $society = $client->createSociety($societyInput);
    var_dump($society);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
