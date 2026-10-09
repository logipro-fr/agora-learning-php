<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Identifier\AgoraId;

class EnrollmentTest extends AbstractAgoraLearningTest
{
    public function testCreateEnrollment(): void
    {
        //Arrange
        $learnerCreated = $this->client->createLearner(
            new LearnerInput($this->newExternalId('L'), 'Dupont', 'Jean', 'jean.dupont.enr@example.com', 'jean.dupont.enr@example.com')
        );
        $sessionOutput = $this->client->createSession(new SessionInput(
            $this->newExternalId('S'),
            'Session Enrollment Test',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-09-01'),
                new \DateTimeImmutable('2026-09-05'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        ));

        $enrollmentInput = new EnrollmentInput(
            $this->newExternalId('E'),
            $sessionOutput->agoraId->getId(),
            $learnerCreated->agoraId->getId(),
            true
        );

        //Act
        $created = $this->client->createEnrollment($enrollmentInput);
        $enrollmentOutput = $this->client->getEnrollment($created->agoraId);

        // Assert
        $this->assertEquals($created->agoraId->getId(), $enrollmentOutput->uuid);
        $this->assertEquals($learnerCreated->agoraId->getId(), $enrollmentOutput->learner->uuid);
        $this->assertEquals($sessionOutput->agoraId->getId(), $enrollmentOutput->session->uuid);
    }

    public function testGetEnrollment(): void
    {
        //Arrange
        $learnerCreated = $this->client->createLearner(
            new LearnerInput($this->newExternalId('L'), 'Martin', 'Luc', 'luc.martin.enr@example.com', 'luc.martin.enr@example.com')
        );
        $sessionOutput = $this->client->createSession(new SessionInput(
            $this->newExternalId('S'),
            'Session Get Enrollment Test',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-09-10'),
                new \DateTimeImmutable('2026-09-12'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        ));
        $created = $this->client->createEnrollment(
            new EnrollmentInput($this->newExternalId('E'), $sessionOutput->agoraId->getId(), $learnerCreated->agoraId->getId(), false)
        );
        $uuid = $created->agoraId->getId();

        //Act
        $enrollmentOutput = $this->client->getEnrollment(new AgoraId($uuid));

        // Assert
        $this->assertEquals($uuid, $enrollmentOutput->uuid);
        $this->assertEquals($learnerCreated->agoraId->getId(), $enrollmentOutput->learner->uuid);
        $this->assertEquals($sessionOutput->agoraId->getId(), $enrollmentOutput->session->uuid);
    }

    public function testGetCollectionEnrollmentFromSession(): void
    {
        //Arrange - 1 session, 2 apprenants
        $sessionOutput = $this->client->createSession(new SessionInput(
            $this->newExternalId('S'),
            'Session For Enrollment Collection Test',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-09-15'),
                new \DateTimeImmutable('2026-09-19'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        ));

        $learnerCreated1 = $this->client->createLearner(
            new LearnerInput($this->newExternalId('L'), 'Simon', 'Paul', 'paul.simon.enr1@example.com', 'paul.simon.enr1@example.com')
        );
        $learnerCreated2 = $this->client->createLearner(
            new LearnerInput($this->newExternalId('L'), 'Garfunkel', 'Art', 'art.garfunkel.enr2@example.com', 'art.garfunkel.enr2@example.com')
        );

        $enrollment1 = $this->client->createEnrollment(
            new EnrollmentInput($this->newExternalId('E'), $sessionOutput->agoraId->getId(), $learnerCreated1->agoraId->getId(), false)
        );
        $enrollment2 = $this->client->createEnrollment(
            new EnrollmentInput($this->newExternalId('E'), $sessionOutput->agoraId->getId(), $learnerCreated2->agoraId->getId(), false)
        );

        //Act
        $enrollmentOutputs = $this->client->getCollectionEnrollmentFromSession($sessionOutput->agoraId);

        // Assert
        foreach ($enrollmentOutputs as $enrollmentOutput) {
            if ($enrollment1->agoraId->getId() === $enrollmentOutput->uuid) {
                $this->assertEquals($enrollment1->agoraId->getId(), $enrollmentOutput->uuid);
                $this->assertEquals($learnerCreated1->agoraId->getId(), $enrollmentOutput->learner->uuid);
                $this->assertEquals($sessionOutput->agoraId->getId(), $enrollmentOutput->session->uuid);
            } elseif ($enrollment2->agoraId->getId() === $enrollmentOutput->uuid) {
                $this->assertEquals($enrollment2->agoraId->getId(), $enrollmentOutput->uuid);
                $this->assertEquals($learnerCreated2->agoraId->getId(), $enrollmentOutput->learner->uuid);
                $this->assertEquals($sessionOutput->agoraId->getId(), $enrollmentOutput->session->uuid);
            }
        }
    }

    public function testGetCollectionEnrollmentFromLearner(): void
    {
        //Arrange - 1 apprenant, 2 sessions
        $learnerCreated = $this->client->createLearner(
            new LearnerInput($this->newExternalId('L'), 'Lennon', 'John', 'john.lennon.enr@example.com', 'john.lennon.enr@example.com')
        );

        $sessionOutput1 = $this->client->createSession(new SessionInput(
            $this->newExternalId('S'),
            'Session FIXED Learner Collection Test 1',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-10-01'),
                new \DateTimeImmutable('2026-10-03'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        ));
        $sessionOutput2 = $this->client->createSession(new SessionInput(
            $this->newExternalId('S'),
            'Session OPENED Learner Collection Test 2',
            new OpenedSessionInput(
                new \DateTimeImmutable('2026-11-01'),
                new \DateTimeImmutable('2026-11-30'),
                30
            )
        ));

        $enrollment1 = $this->client->createEnrollment(
            new EnrollmentInput($this->newExternalId('E'), $sessionOutput1->agoraId->getId(), $learnerCreated->agoraId->getId(), false)
        );
        $enrollment2 = $this->client->createEnrollment(
            new EnrollmentInput($this->newExternalId('E'), $sessionOutput2->agoraId->getId(), $learnerCreated->agoraId->getId(), false)
        );

        //Act
        $enrollmentOutputs = $this->client->getCollectionEnrollmentFromLearner($learnerCreated->agoraId);

        // Assert
        foreach ($enrollmentOutputs as $enrollmentOutput) {
            if ($enrollment1->agoraId->getId() === $enrollmentOutput->uuid) {
                $this->assertEquals($enrollment1->agoraId->getId(), $enrollmentOutput->uuid);
                $this->assertEquals($learnerCreated->agoraId->getId(), $enrollmentOutput->learner->uuid);
                $this->assertEquals($sessionOutput1->agoraId->getId(), $enrollmentOutput->session->uuid);
            } elseif ($enrollment2->agoraId->getId() === $enrollmentOutput->uuid) {
                $this->assertEquals($enrollment2->agoraId->getId(), $enrollmentOutput->uuid);
                $this->assertEquals($learnerCreated->agoraId->getId(), $enrollmentOutput->learner->uuid);
                $this->assertEquals($sessionOutput2->agoraId->getId(), $enrollmentOutput->session->uuid);
            }
        }
    }

    public function testGetEnrollmentFromSessionAndLearner(): void
    {
        //Arrange
        $learnerCreated = $this->client->createLearner(
            new LearnerInput($this->newExternalId('L'), 'McCartney', 'Paul', 'paul.mccartney.enr@example.com', 'paul.mccartney.enr@example.com')
        );
        $sessionOutput = $this->client->createSession(new SessionInput(
            $this->newExternalId('S'),
            'Session For Enrollment By Session And Learner Test',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-09-22'),
                new \DateTimeImmutable('2026-09-26'),
                SessionType::SESSION_TYPE_INTRA,
                SessionMode::SESSION_MODE_DISTANCE
            )
        ));
        $created = $this->client->createEnrollment(
            new EnrollmentInput($this->newExternalId('E'), $sessionOutput->agoraId->getId(), $learnerCreated->agoraId->getId(), false)
        );

        //Act
        $enrollmentOutput = $this->client->getEnrollmentFromSessionAndLearner(
            $sessionOutput->agoraId,
            $learnerCreated->agoraId
        );

        // Assert
        $this->assertEquals($created->agoraId->getId(), $enrollmentOutput->uuid);
        $this->assertEquals($learnerCreated->agoraId->getId(), $enrollmentOutput->learner->uuid);
        $this->assertEquals($sessionOutput->agoraId->getId(), $enrollmentOutput->session->uuid);
    }
}
