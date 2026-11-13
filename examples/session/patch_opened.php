<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionPatchInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

// Session ouverte existante, désignée par son identifiant externe (ou new AgoraId('ses_...'))
$identifier = new ExternalId('MonERP', uniqid('S-'));
$client->createSession(new SessionInput(
    $identifier,
    'Formation e-learning',
    new OpenedSessionInput(new \DateTimeImmutable('now'), new \DateTimeImmutable('+6 months'), 30)
));

// PATCH : allonge la durée d'accès de chaque apprenant
$patch = (new OpenedSessionPatchInput())
    ->setAccessDurationDays(60);

try {
    $updated = $client->patchSession($identifier, $patch);
    var_dump($updated);
    var_dump($client->getSession($identifier));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
