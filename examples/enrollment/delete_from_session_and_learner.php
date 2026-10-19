<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

try {
    // Désinscrit l'apprenant de la session ; une inscription inconnue lève une AgoraLearningClientException (code 404)
    $client->deleteEnrollmentFromSessionAndLearner(new ExternalId('MonERP', 'S-42'), new ExternalId('MonERP', 'L-42'));
    echo 'Inscription supprimée' . PHP_EOL;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
