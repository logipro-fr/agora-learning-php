<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

try {
    // Vide le programme de la session (la session est conservée) ; une session inconnue lève une AgoraLearningClientException (code 404)
    $client->deleteProgram(new ExternalId('MonERP', 'S-42'));
    echo 'Programme vidé' . PHP_EOL;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
