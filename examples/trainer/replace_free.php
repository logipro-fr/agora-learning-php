<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// Formateur existant, désigné par son identifiant externe (ou new AgoraId('per_...'))
// L'e-mail est l'identifiant du compte du formateur : il doit être unique
$identifier = new ExternalId('MonERP', uniqid('T-'));
$email = uniqid('formateur.') . '@example.com';
$client->createTrainerFree(new TrainerFreeInput($identifier, 'Martin', 'Sophie', $email));

// PUT : remplace tout le formateur ; notifyUser est ignoré (pris en compte uniquement à la création)
$trainerInput = new TrainerFreeInput(
    $identifier,
    'Martin',
    'Sophie',
    $email,
    false
);

try {
    $updated = $client->replaceTrainerFree($identifier, $trainerInput);
    var_dump($updated);
    var_dump($client->getTrainer($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
