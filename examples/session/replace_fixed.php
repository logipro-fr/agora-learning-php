<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// Session à dates fixes existante, désignée par son identifiant externe (ou new AgoraId('ses_...'))
$identifier = new ExternalId('MonERP', uniqid('S-'));
$client->createSession(new SessionInput(
    $identifier,
    'Formation PHP',
    new FixedSessionInput(
        new \DateTimeImmutable('+1 month'),
        new \DateTimeImmutable('+1 month +4 days'),
        SessionType::SESSION_TYPE_INTRA,
        SessionMode::SESSION_MODE_FACE_TO_FACE
    )
));

// PUT : remplace toute la session. Le type de sessionData (fixe / ouverte) doit être celui de la session
$sessionInput = new SessionInput(
    $identifier,
    'Formation PHP avancée',
    new FixedSessionInput(
        new \DateTimeImmutable('+2 months'),
        new \DateTimeImmutable('+2 months +4 days'),
        SessionType::SESSION_TYPE_INTER,
        SessionMode::SESSION_MODE_BLENDED
    )
);

try {
    $updated = $client->replaceSession($identifier, $sessionInput);
    var_dump($updated);
    var_dump($client->getSession($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
