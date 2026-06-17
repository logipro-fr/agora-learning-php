<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$fixedSessionData = new FixedSessionInput(
    new \DateTimeImmutable('2026-09-01'),
    new \DateTimeImmutable('2026-09-05'),
    SessionType::SESSION_TYPE_INTER,
    SessionMode::SESSION_MODE_FACE_TO_FACE
);

$sessionInput = new SessionInput(
    'Formation PHP avancé',
    $fixedSessionData,
    false,
    true,
    true,
    1500.0,
    28800,
    'Formation intensive sur les bonnes pratiques PHP',
    12,
    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
    'https://example.com/survey/pre',
    'https://example.com/survey/spot',
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
