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

// Les responsables sont des contacts existants, désignés par leur identifiant (Agora ou "source:id")
$legalPersonId = new ExternalId('MonERP', uniqid('P-'));
$client->createPerson(new PersonInput(
    $legalPersonId,
    'Durand',
    'Paul',
    'paul.durand@example.com',
    Gender::GENDER_MALE,
    '0123456789'
));

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
    $legalPersonId->getId(),     // legalPersonId
    null,                        // administrativePersonId
    null,                        // rhPersonId
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
