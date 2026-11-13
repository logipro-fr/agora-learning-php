<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

try {
    // Détache le formateur de l'application source (il reste sur Agora, désignable par son UUID) ;
    // un formateur inconnu lève une AgoraLearningClientException (code 404)
    $client->detachTrainer(new ExternalId('MonERP', 'T-42'));
    echo 'Formateur détaché' . PHP_EOL;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
