<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Input\Learner\LearnerPatchInput;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// Apprenant existant, désigné par son identifiant externe (ou new AgoraId('per_...'))
$identifier = new ExternalId('MonERP', uniqid('L-'));
$client->createLearner(new LearnerInput($identifier, 'Lennon', 'John', 'john.lennon@example.com', 'john.lennon@example.com'));

// PATCH : seuls les champs renseignés sont modifiés
$patch = (new LearnerPatchInput())
    ->setAddressLocality('Liverpool')
    ->setAddressCountry('Royaume-Uni');

try {
    $updated = $client->patchLearner($identifier, $patch);
    var_dump($updated);
    var_dump($client->getLearner($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
