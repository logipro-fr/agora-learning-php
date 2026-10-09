<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$personInput = new PersonInput(
    new ExternalId('MonERP', uniqid('P-')),
    'Dupont',
    'Jean',
    'jean.dupont@example.com',
    Gender::GENDER_MALE,
    '0123456789',
    '3 rue de la Paix',
    '75001',
    'Paris',
    'France',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/homme.jpg',
    new \DateTimeImmutable('1985-06-15'),
    'Développeur'
);

try {
    $created = $client->createPerson($personInput);
    var_dump($created);
    var_dump($client->getPerson($created->agoraId));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
