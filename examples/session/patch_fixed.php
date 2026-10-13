<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionPatchInput;
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

// PATCH : décale la date de fin et limite le nombre de places, le reste est inchangé
$patch = (new FixedSessionPatchInput())
    ->setAvailabilityEndDate(new \DateTimeImmutable('+1 month +6 days'))
    ->setMaxPlaces(12);

try {
    $updated = $client->patchSession($identifier, $patch);
    var_dump($updated);
    var_dump($client->getSession($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
