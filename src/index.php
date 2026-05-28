<?php

declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\Enum\Gender;

$client = new AgoraLearningClient(
    'https://symfony74.agora-learning.com',
    '8f7a5c3a12e4c1445b281ebb2c0ac8012081bc6f8931dd9d149d352a1458f8c4'
    // 'http://agora-webserver/phoenix',
    // '63bacd7eb8c4851d19b2d15057a4e9b6908dc702ff4b06883ed4913b7129a1be'
    // 'dc52fc4a100d636cdc348a74b4bbd7933f16f43c4d406f82d42984eeea76281f'
);

try {
    $pingResponse = $client->getCollectionPerson();
    var_dump($pingResponse);
    die();
    // $pingResponse = $client->createTrainerEmployee(new TrainerEmployeeInput(
    //     'FamilyName',
    //     'givernName',
    //     'Trainer.Employee2@api.com',
    //     true,
    //     true,
    //     Gender::GENDER_MALE,
    //     '012345678',
    //     '012345678',
    //     '3 rue du',
    //     '43000',
    //     'Puit',
    //     'Fronce',
    //     'image.jpeg',
    //     new \DateTimeImmutable('now'),
    //     'poissonier',
    //     'soc_01KRXPMF93F039K58SC9QEZZNY',
    //     'cv.pdf',
    //     'degree.pdf',
    //     'contract.pdf',
    //     'on vend du poisson'
    // ));
    // var_dump($pingResponse);
    // die();

    // $pingResponse = $client->ping();
    // $statusCode = $pingResponse->getStatusCode();
    // if ($statusCode !== 200) {
    //     echo 'Impossible de joindre l\'API Phoenix (HTTP ' . $statusCode . ')' . PHP_EOL;
    //     echo $pingResponse->getContent(true) . PHP_EOL;
    //     exit(1);
    // }
    // echo "Connexion à l'API OK" . PHP_EOL;
} catch (\Exception $e) {
    echo 'Erreur : ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
