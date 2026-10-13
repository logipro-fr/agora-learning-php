<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyPatchInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// Société existante, désignée par son identifiant externe (ou new AgoraId('soc_...'))
$identifier = new ExternalId('MonERP', uniqid('SOC-'));
$client->createSociety(new SocietyInput($identifier, 'Formation Excellence SAS'));

// Responsable RH : un contact existant, désigné par son identifiant
$rhPersonId = new ExternalId('MonERP', uniqid('P-'));
$client->createPerson(new PersonInput($rhPersonId, 'Girard', 'Marie', 'marie.girard@example.com', Gender::GENDER_FEMALE));

// PATCH : seuls les champs renseignés sont modifiés ; setRhPersonId(null) retirerait le responsable
$patch = (new SocietyPatchInput())
    ->setTelephone('0145678901')
    ->setRhPersonId($rhPersonId->getId());

try {
    $updated = $client->patchSociety($identifier, $patch);
    var_dump($updated);
    var_dump($client->getSociety($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
