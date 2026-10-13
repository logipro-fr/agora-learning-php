<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreePatchInput;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// Formateur indépendant existant, désigné par son identifiant externe (ou new AgoraId('per_...'))
$identifier = new ExternalId('MonERP', uniqid('T-'));
$client->createTrainerFree(new TrainerFreeInput($identifier, 'Bernard', 'Pierre', uniqid('formateur.') . '@example.com'));

// PATCH : seuls les champs renseignés sont modifiés
$patch = (new TrainerFreePatchInput())
    ->setHourlyCost(65.0)
    ->setDaylyCost(450.0);

try {
    $updated = $client->patchTrainerFree($identifier, $patch);
    var_dump($updated);
    var_dump($client->getTrainer($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
