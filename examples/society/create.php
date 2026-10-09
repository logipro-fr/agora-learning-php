<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// $legalPerson = new PersonInput(
//     new ExternalId('MonERP', uniqid('P-')),
//     'Durand',
//     'Paul',
//     'paul.durand@example.com',
//     Gender::GENDER_MALE,
//     '0123456789'
// );

// $administrativePerson = new PersonInput(
//     new ExternalId('MonERP', uniqid('P-')),
//     'Girard',
//     'Marie',
//     'marie.girard@example.com',
//     Gender::GENDER_FEMALE,
//     '0987654321'
// );
$legalPerson = $administrativePerson = null;

$societyInput = new SocietyInput(
    new ExternalId('MonERP', uniqid('SOC-')),
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
    $created = $client->createSociety($societyInput);
    var_dump($created);
    var_dump($client->getSociety($created->agoraId));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
