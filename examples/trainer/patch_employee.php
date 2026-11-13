<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeePatchInput;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// Formateur employé existant, désigné par son identifiant externe (ou new AgoraId('per_...'))
$identifier = new ExternalId('MonERP', uniqid('T-'));
$client->createTrainerEmployee(new TrainerEmployeeInput($identifier, 'Martin', 'Sophie', uniqid('formateur.') . '@example.com'));

// PATCH : seuls les champs renseignés sont modifiés, null retire la société
$patch = (new TrainerEmployeePatchInput())
    ->setJobTitle('Formatrice PHP')
    ->setSocietyId(null);

try {
    $updated = $client->patchTrainerEmployee($identifier, $patch);
    var_dump($updated);
    var_dump($client->getTrainer($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
