<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// Société existante, désignée par son identifiant externe (ou new AgoraId('soc_...'))
$identifier = new ExternalId('MonERP', uniqid('SOC-'));
$client->createSociety(new SocietyInput($identifier, 'Formation Excellence SAS', '12345678901234', 'SAS'));

// PUT : remplace toute la société, les champs et responsables non renseignés sont vidés
$societyInput = new SocietyInput(
    $identifier,
    'Formation Excellence SA',
    '12345678901234',
    'SA'
);

try {
    $updated = $client->replaceSociety($identifier, $societyInput);
    var_dump($updated);
    var_dump($client->getSociety($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
