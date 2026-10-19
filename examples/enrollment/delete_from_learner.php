<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

try {
    // Supprime toutes les inscriptions de l'apprenant
    $client->deleteCollectionEnrollmentFromLearner(new ExternalId('MonERP', 'L-42'));
    echo 'Inscriptions de l\'apprenant supprimées' . PHP_EOL;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
