<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;

class SessionTest extends AbstractAgoraLearningTest
{
    public function testCreateCompleteFixedSession(): void
    {
        //Arrange - créer les dépendances
        $trainerCreated = $this->client->createTrainerEmployee(
            new TrainerEmployeeInput($this->newExternalId('T'), 'Formateur', 'Session', 'formateur.session.fixed@example.com')
        );
        $personCreated = $this->client->createPerson(
            new PersonInput($this->newExternalId('P'), 'Admin', 'Session', 'administrateur.session.fixed@example.com', Gender::GENDER_NA)
        );

        $title = 'Formation PHP avancé';
        $startDate = new \DateTimeImmutable('2026-09-01');
        $endDate = new \DateTimeImmutable('2026-09-05');
        $type = SessionType::SESSION_TYPE_INTER;
        $mode = SessionMode::SESSION_MODE_FACE_TO_FACE;
        $manualDuration = true;
        $hasForum = true;
        $notifyMailForum = true;
        $price = 1500.0;
        $durationInSeconds = 28800;
        $description = 'Formation intensive sur les bonnes pratiques PHP';
        $maxPlaces = 12;
        $urlPreTrainingSurvey = 'https://example.com/survey/pre';
        $urlOnTheSpotSurvey = 'https://example.com/survey/spot';
        $urlDelayedSurvey = 'https://example.com/survey/delayed';
        $image = 'https://www.agora-learning.com/apiAgoraLearning/ressources/formation.jpg';

        $sessionInput = new SessionInput(
            $this->newExternalId('S'),
            $title,
            new FixedSessionInput($startDate, $endDate, $type, $mode),
            $manualDuration,
            $hasForum,
            $notifyMailForum,
            $price,
            $durationInSeconds,
            $description,
            $maxPlaces,
            $trainerCreated->agoraId->getId(),
            $personCreated->agoraId->getId(),
            $urlPreTrainingSurvey,
            $urlOnTheSpotSurvey,
            $urlDelayedSurvey,
            $image
        );

        //Act
        $created = $this->client->createSession($sessionInput);
        $sessionOutput = $this->client->getSession($created->agoraId);

        // Assert
        $this->assertEquals($title, $sessionOutput->title);
        $this->assertEquals($type, $sessionOutput->type);
        $this->assertEquals($mode, $sessionOutput->mode);
        $this->assertEquals($manualDuration, $sessionOutput->manualDuration);
        $this->assertEquals($hasForum, $sessionOutput->hasForum);
        $this->assertEquals($notifyMailForum, $sessionOutput->notifyMailForum);
        $this->assertEquals($price, $sessionOutput->price);
        $this->assertEquals($durationInSeconds, $sessionOutput->duration);
        $this->assertEquals($description, $sessionOutput->description);
        $this->assertEquals($maxPlaces, $sessionOutput->maxPlaces);
        $this->assertNotNull($sessionOutput->pedagogicalTrainer);
        $this->assertEquals($trainerCreated->agoraId->getId(), $sessionOutput->pedagogicalTrainer->uuid);
        $this->assertNotNull($sessionOutput->administrativePerson);
        $this->assertEquals($personCreated->agoraId->getId(), $sessionOutput->administrativePerson->uuid);
        $this->assertEquals($urlPreTrainingSurvey, $sessionOutput->urlPreTrainingSurvey);
        $this->assertEquals($urlOnTheSpotSurvey, $sessionOutput->urlOnTheSpotSurvey);
        $this->assertEquals($urlDelayedSurvey, $sessionOutput->urlDelayedSurvey);
        $this->assertInstanceOf(FixedSessionOutput::class, $sessionOutput->sessionData);
        /** @var FixedSessionOutput $sessionData */
        $sessionData = $sessionOutput->sessionData;
        $this->assertEquals($startDate->format('Y-m-d'), $sessionData->availabilityStartDate->format('Y-m-d'));
        $this->assertEquals($endDate->format('Y-m-d'), $sessionData->availabilityEndDate->format('Y-m-d'));
    }

    public function testCreateMinimumFixedSession(): void
    {
        //Arrange
        $title = 'Formation PHP Minimum';
        $startDate = new \DateTimeImmutable('2026-10-01');
        $endDate = new \DateTimeImmutable('2026-10-05');
        $type = SessionType::SESSION_TYPE_INTRA;
        $mode = SessionMode::SESSION_MODE_BLENDED;

        $sessionInput = new SessionInput(
            $this->newExternalId('S'),
            $title,
            new FixedSessionInput($startDate, $endDate, $type, $mode)
        );

        //Act
        $created = $this->client->createSession($sessionInput);
        $sessionOutput = $this->client->getSession($created->agoraId);

        // Assert
        $this->assertEquals($title, $sessionOutput->title);
        $this->assertEquals($type, $sessionOutput->type);
        $this->assertEquals($mode, $sessionOutput->mode);
        $this->assertNull($sessionOutput->description);
        $this->assertNull($sessionOutput->maxPlaces);
        $this->assertNull($sessionOutput->pedagogicalTrainer);
        $this->assertNull($sessionOutput->administrativePerson);
        $this->assertNull($sessionOutput->urlPreTrainingSurvey);
        $this->assertNull($sessionOutput->urlOnTheSpotSurvey);
        $this->assertNull($sessionOutput->urlDelayedSurvey);
        $this->assertInstanceOf(FixedSessionOutput::class, $sessionOutput->sessionData);
        /** @var FixedSessionOutput $sessionData */
        $sessionData = $sessionOutput->sessionData;
        $this->assertEquals($startDate->format('Y-m-d'), $sessionData->availabilityStartDate->format('Y-m-d'));
        $this->assertEquals($endDate->format('Y-m-d'), $sessionData->availabilityEndDate->format('Y-m-d'));
    }

    public function testCreateCompleteOpenedSession(): void
    {
        //Arrange - créer les dépendances
        $trainerCreated = $this->client->createTrainerEmployee(
            new TrainerEmployeeInput($this->newExternalId('T'), 'Formateur', 'Opened', 'formateur.opened.complete@example.com')
        );
        $personCreated = $this->client->createPerson(
            new PersonInput($this->newExternalId('P'), 'Admin', 'Opened', 'administrateur.session.fixed@example.com', Gender::GENDER_NA)
        );

        $title = 'E-learning : Introduction au management';
        $subscribeStartDate = new \DateTimeImmutable('2026-07-01');
        $subscribeEndDate = new \DateTimeImmutable('2026-12-31');
        $accessDurationDays = 90;
        $manualDuration = true;
        $hasForum = true;
        $notifyMailForum = false;
        $price = 299.0;
        $durationInSeconds = 14400;
        $description = 'Parcours e-learning en accès libre sur 90 jours';
        $urlDelayedSurvey = 'https://example.com/survey/delayed';
        $image = 'https://www.agora-learning.com/apiAgoraLearning/ressources/formation.jpg';

        $sessionInput = new SessionInput(
            $this->newExternalId('S'),
            $title,
            new OpenedSessionInput($subscribeStartDate, $subscribeEndDate, $accessDurationDays),
            $manualDuration,
            $hasForum,
            $notifyMailForum,
            $price,
            $durationInSeconds,
            $description,
            null,
            $trainerCreated->agoraId->getId(),
            $personCreated->agoraId->getId(),
            null,
            null,
            $urlDelayedSurvey,
            $image
        );

        //Act
        $created = $this->client->createSession($sessionInput);
        $sessionOutput = $this->client->getSession($created->agoraId);

        // Assert
        $this->assertEquals($title, $sessionOutput->title);
        $this->assertEquals(SessionType::SESSION_TYPE_INTER, $sessionOutput->type);
        $this->assertEquals(SessionMode::SESSION_MODE_E_LEARNING, $sessionOutput->mode);
        $this->assertEquals($manualDuration, $sessionOutput->manualDuration);
        $this->assertEquals($hasForum, $sessionOutput->hasForum);
        $this->assertEquals($notifyMailForum, $sessionOutput->notifyMailForum);
        $this->assertEquals($price, $sessionOutput->price);
        $this->assertEquals($durationInSeconds, $sessionOutput->duration);
        $this->assertEquals($description, $sessionOutput->description);
        $this->assertNull($sessionOutput->maxPlaces);
        $this->assertNotNull($sessionOutput->pedagogicalTrainer);
        $this->assertEquals($trainerCreated->agoraId->getId(), $sessionOutput->pedagogicalTrainer->uuid);
        $this->assertNotNull($sessionOutput->administrativePerson);
        $this->assertEquals($personCreated->agoraId->getId(), $sessionOutput->administrativePerson->uuid);
        $this->assertNull($sessionOutput->urlPreTrainingSurvey);
        $this->assertNull($sessionOutput->urlOnTheSpotSurvey);
        $this->assertEquals($urlDelayedSurvey, $sessionOutput->urlDelayedSurvey);
        $this->assertInstanceOf(OpenedSessionOutput::class, $sessionOutput->sessionData);
        /** @var OpenedSessionOutput $sessionData */
        $sessionData = $sessionOutput->sessionData;
        $this->assertEquals($subscribeStartDate->format('Y-m-d'), $sessionData->subscribeStartDate->format('Y-m-d'));
        $this->assertEquals($subscribeEndDate->format('Y-m-d'), $sessionData->subscribeEndDate->format('Y-m-d'));
        $this->assertEquals($accessDurationDays, $sessionData->accessDurationDays);
    }

    public function testCreateMinimumOpenedSession(): void
    {
        //Arrange
        $title = 'E-learning Minimum';
        $subscribeStartDate = new \DateTimeImmutable('2026-11-01');
        $subscribeEndDate = new \DateTimeImmutable('2026-11-30');
        $accessDurationDays = 30;

        $sessionInput = new SessionInput(
            $this->newExternalId('S'),
            $title,
            new OpenedSessionInput($subscribeStartDate, $subscribeEndDate, $accessDurationDays)
        );

        //Act
        $created = $this->client->createSession($sessionInput);
        $sessionOutput = $this->client->getSession($created->agoraId);

        // Assert
        $this->assertEquals($title, $sessionOutput->title);
        $this->assertEquals(SessionType::SESSION_TYPE_INTER, $sessionOutput->type);
        $this->assertEquals(SessionMode::SESSION_MODE_E_LEARNING, $sessionOutput->mode);
        $this->assertNull($sessionOutput->description);
        $this->assertNull($sessionOutput->maxPlaces);
        $this->assertNull($sessionOutput->pedagogicalTrainer);
        $this->assertNull($sessionOutput->administrativePerson);
        $this->assertNull($sessionOutput->urlPreTrainingSurvey);
        $this->assertNull($sessionOutput->urlOnTheSpotSurvey);
        $this->assertNull($sessionOutput->urlDelayedSurvey);
        $this->assertInstanceOf(OpenedSessionOutput::class, $sessionOutput->sessionData);
        /** @var OpenedSessionOutput $sessionData */
        $sessionData = $sessionOutput->sessionData;
        $this->assertEquals($subscribeStartDate->format('Y-m-d'), $sessionData->subscribeStartDate->format('Y-m-d'));
        $this->assertEquals($subscribeEndDate->format('Y-m-d'), $sessionData->subscribeEndDate->format('Y-m-d'));
        $this->assertEquals($accessDurationDays, $sessionData->accessDurationDays);
    }

    public function testGetSession(): void
    {
        //Arrange
        $title = 'Formation Get Session Test';
        $sessionInput = new SessionInput(
            $this->newExternalId('S'),
            $title,
            new FixedSessionInput(
                new \DateTimeImmutable('2026-10-10'),
                new \DateTimeImmutable('2026-10-14'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        );
        $output = $this->client->createSession($sessionInput);
        $uuid = $output->agoraId->getId();

        //Act
        $sessionOutput = $this->client->getSession(new AgoraId($uuid));

        // Assert
        $this->assertEquals($uuid, $sessionOutput->uuid);
        $this->assertEquals($title, $sessionOutput->title);
    }

    public function testGetCollectionSession(): void
    {
        //Arrange - une session fixe et une session ouverte
        $title1 = 'Session Fixe Collection Test';
        $sessionInput1 = new SessionInput(
            $this->newExternalId('S'),
            $title1,
            new FixedSessionInput(
                new \DateTimeImmutable('2026-10-20'),
                new \DateTimeImmutable('2026-10-24'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        );
        $output1 = $this->client->createSession($sessionInput1);
        $uuid1 = $output1->agoraId->getId();

        $title2 = 'Session Ouverte Collection Test';
        $sessionInput2 = new SessionInput(
            $this->newExternalId('S'),
            $title2,
            new OpenedSessionInput(
                new \DateTimeImmutable('2026-12-01'),
                new \DateTimeImmutable('2026-12-31'),
                60
            )
        );
        $output2 = $this->client->createSession($sessionInput2);
        $uuid2 = $output2->agoraId->getId();

        //Act
        $sessionOutputs = $this->client->getCollectionSession();

        // Assert
        foreach ($sessionOutputs as $sessionOutput) {
            if ($uuid1 === $sessionOutput->uuid) {
                $this->assertEquals($uuid1, $sessionOutput->uuid);
                $this->assertEquals($title1, $sessionOutput->title);
                $this->assertInstanceOf(FixedSessionOutput::class, $sessionOutput->sessionData);
            } elseif ($uuid2 === $sessionOutput->uuid) {
                $this->assertEquals($uuid2, $sessionOutput->uuid);
                $this->assertEquals($title2, $sessionOutput->title);
                $this->assertInstanceOf(OpenedSessionOutput::class, $sessionOutput->sessionData);
            }
        }
    }

    /**
     * @group external-id
     */
    public function testGetSessionByExternalId(): void
    {
        //Arrange
        $identifier = new ExternalId('IntegrationTest', 'S-42');

        //Act
        try {
            $sessionOutput = $this->client->getSession($identifier);
        } catch (AgoraLearningClientException $e) {
            if (in_array($e->getCode(), [400, 404], true)) {
                $this->markTestSkipped(sprintf(
                    'GET /sessions/%s non supporté par le serveur (HTTP %d) : %s',
                    rawurlencode($identifier->getId()),
                    $e->getCode(),
                    $e->getMessage()
                ));
            }
            throw $e;
        }

        // Assert
        $this->assertNotSame('', $sessionOutput->uuid);
    }
}
