<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Person\PersonPatchInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// Contact existant, désigné par son identifiant externe (ou new AgoraId('per_...'))
$identifier = new ExternalId('MonERP', uniqid('P-'));
$client->createPerson(new PersonInput($identifier, 'Dupont', 'Jean', 'jean.dupont@example.com', Gender::GENDER_MALE));

// PATCH : seuls les champs renseignés sont modifiés, null vide le champ
$patch = (new PersonPatchInput())
    ->setTelephone('0102030405')
    ->setJobTitle(null);

try {
    $updated = $client->patchPerson($identifier, $patch);
    var_dump($updated);
    var_dump($client->getPerson($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
