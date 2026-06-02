<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$openedSessionData = new OpenedSessionInput(
    new \DateTimeImmutable('2026-07-01'),
    new \DateTimeImmutable('2026-12-31'),
    90
);

$sessionInput = new SessionInput(
    'E-learning : Introduction au management',
    $openedSessionData,
    false,
    true,
    false,
    299.0,
    14400,
    'Parcours e-learning en accès libre sur 90 jours',
    null,
    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
    null,
    null,
    'https://example.com/survey/delayed',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/formation.jpg'
);

try {
    $session = $client->createSession($sessionInput);
    var_dump($session);
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
