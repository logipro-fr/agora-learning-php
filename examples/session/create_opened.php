<?php

declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\Identifier\ExternalId;

$client = new AgoraLearningClient($config['url'], $config['api_key']);

$openedSessionData = new OpenedSessionInput(
    new \DateTimeImmutable('today'),
    new \DateTimeImmutable('today +1 year'),
    90
);

$sessionInput = new SessionInput(
    new ExternalId('MonERP', uniqid('S-')),
    'E-learning : Introduction au management',
    $openedSessionData,
    false,
    true,
    false,
    299.0,
    14400,
    'Parcours e-learning en accès libre sur 90 jours',
    null,
    null,
    null,
    null,
    null,
    'https://example.com/survey/delayed',
    'https://www.agora-learning.com/apiAgoraLearning/ressources/formation.jpg'
);

try {
    $created = $client->createSession($sessionInput);
    var_dump($created);
    var_dump($client->getSession($created->agoraId));
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
