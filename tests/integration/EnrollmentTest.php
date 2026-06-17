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

class EnrollmentTest extends AbstractAgoraLearningTest
{
    public function testCreateEnrollment(): void
    {
        //Arrange
        $learnerOutput = $this->client->createLearner(
            new LearnerInput('Dupont', 'Jean', 'jean.dupont.enr@example.com', 'jean.dupont.enr@example.com')
        );
        $sessionOutput = $this->client->createSession(new SessionInput(
            'Session Enrollment Test',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-09-01'),
                new \DateTimeImmutable('2026-09-05'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        ));

        $enrollmentInput = new EnrollmentInput(
            $sessionOutput->uuid,
            $learnerOutput->uuid,
            true
        );

        //Act
        $enrollmentOutput = $this->client->createEnrollment($enrollmentInput);

        // Assert
        $this->assertNotEmpty($enrollmentOutput->uuid);
        $this->assertEquals($learnerOutput->uuid, $enrollmentOutput->learner->uuid);
        $this->assertEquals($sessionOutput->uuid, $enrollmentOutput->session->uuid);
    }

    public function testGetEnrollment(): void
    {
        //Arrange
        $learnerOutput = $this->client->createLearner(
            new LearnerInput('Martin', 'Luc', 'luc.martin.enr@example.com', 'luc.martin.enr@example.com')
        );
        $sessionOutput = $this->client->createSession(new SessionInput(
            'Session Get Enrollment Test',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-09-10'),
                new \DateTimeImmutable('2026-09-12'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        ));
        $created = $this->client->createEnrollment(
            new EnrollmentInput($sessionOutput->uuid, $learnerOutput->uuid, false)
        );
        $uuid = $created->uuid;

        //Act
        $enrollmentOutput = $this->client->getEnrollment($uuid);

        // Assert
        $this->assertEquals($uuid, $enrollmentOutput->uuid);
        $this->assertEquals($learnerOutput->uuid, $enrollmentOutput->learner->uuid);
        $this->assertEquals($sessionOutput->uuid, $enrollmentOutput->session->uuid);
    }

    public function testGetCollectionEnrollmentFromSession(): void
    {
        //Arrange - 1 session, 2 apprenants
        $sessionOutput = $this->client->createSession(new SessionInput(
            'Session For Enrollment Collection Test',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-09-15'),
                new \DateTimeImmutable('2026-09-19'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        ));

        $learnerOutput1 = $this->client->createLearner(
            new LearnerInput('Simon', 'Paul', 'paul.simon.enr1@example.com', 'paul.simon.enr1@example.com')
        );
        $learnerOutput2 = $this->client->createLearner(
            new LearnerInput('Garfunkel', 'Art', 'art.garfunkel.enr2@example.com', 'art.garfunkel.enr2@example.com')
        );

        $enrollment1 = $this->client->createEnrollment(
            new EnrollmentInput($sessionOutput->uuid, $learnerOutput1->uuid, false)
        );
        $enrollment2 = $this->client->createEnrollment(
            new EnrollmentInput($sessionOutput->uuid, $learnerOutput2->uuid, false)
        );

        //Act
        $enrollmentOutputs = $this->client->getCollectionEnrollmentFromSession($sessionOutput->uuid);

        // Assert
        foreach ($enrollmentOutputs as $enrollmentOutput) {
            if ($enrollment1->uuid === $enrollmentOutput->uuid) {
                $this->assertEquals($enrollment1->uuid, $enrollmentOutput->uuid);
                $this->assertEquals($learnerOutput1->uuid, $enrollmentOutput->learner->uuid);
                $this->assertEquals($sessionOutput->uuid, $enrollmentOutput->session->uuid);
            } elseif ($enrollment2->uuid === $enrollmentOutput->uuid) {
                $this->assertEquals($enrollment2->uuid, $enrollmentOutput->uuid);
                $this->assertEquals($learnerOutput2->uuid, $enrollmentOutput->learner->uuid);
                $this->assertEquals($sessionOutput->uuid, $enrollmentOutput->session->uuid);
            }
        }
    }

    public function testGetCollectionEnrollmentFromLearner(): void
    {
        //Arrange - 1 apprenant, 2 sessions
        $learnerOutput = $this->client->createLearner(
            new LearnerInput('Lennon', 'John', 'john.lennon.enr@example.com', 'john.lennon.enr@example.com')
        );

        $sessionOutput1 = $this->client->createSession(new SessionInput(
            'Session FIXED Learner Collection Test 1',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-10-01'),
                new \DateTimeImmutable('2026-10-03'),
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE
            )
        ));
        $sessionOutput2 = $this->client->createSession(new SessionInput(
            'Session OPENED Learner Collection Test 2',
            new OpenedSessionInput(
                new \DateTimeImmutable('2026-11-01'),
                new \DateTimeImmutable('2026-11-30'),
                30
            )
        ));

        $enrollment1 = $this->client->createEnrollment(
            new EnrollmentInput($sessionOutput1->uuid, $learnerOutput->uuid, false)
        );
        $enrollment2 = $this->client->createEnrollment(
            new EnrollmentInput($sessionOutput2->uuid, $learnerOutput->uuid, false)
        );

        //Act
        $enrollmentOutputs = $this->client->getCollectionEnrollmentFromLearner($learnerOutput->uuid);

        // Assert
        foreach ($enrollmentOutputs as $enrollmentOutput) {
            if ($enrollment1->uuid === $enrollmentOutput->uuid) {
                $this->assertEquals($enrollment1->uuid, $enrollmentOutput->uuid);
                $this->assertEquals($learnerOutput->uuid, $enrollmentOutput->learner->uuid);
                $this->assertEquals($sessionOutput1->uuid, $enrollmentOutput->session->uuid);
            } elseif ($enrollment2->uuid === $enrollmentOutput->uuid) {
                $this->assertEquals($enrollment2->uuid, $enrollmentOutput->uuid);
                $this->assertEquals($learnerOutput->uuid, $enrollmentOutput->learner->uuid);
                $this->assertEquals($sessionOutput2->uuid, $enrollmentOutput->session->uuid);
            }
        }
    }

    public function testGetEnrollmentFromSessionAndLearner(): void
    {
        //Arrange
        $learnerOutput = $this->client->createLearner(
            new LearnerInput('McCartney', 'Paul', 'paul.mccartney.enr@example.com', 'paul.mccartney.enr@example.com')
        );
        $sessionOutput = $this->client->createSession(new SessionInput(
            'Session For Enrollment By Session And Learner Test',
            new FixedSessionInput(
                new \DateTimeImmutable('2026-09-22'),
                new \DateTimeImmutable('2026-09-26'),
                SessionType::SESSION_TYPE_INTRA,
                SessionMode::SESSION_MODE_DISTANCE
            )
        ));
        $created = $this->client->createEnrollment(
            new EnrollmentInput($sessionOutput->uuid, $learnerOutput->uuid, false)
        );

        //Act
        $enrollmentOutput = $this->client->getEnrollmentFromSessionAndLearner(
            $sessionOutput->uuid,
            $learnerOutput->uuid
        );

        // Assert
        $this->assertEquals($created->uuid, $enrollmentOutput->uuid);
        $this->assertEquals($learnerOutput->uuid, $enrollmentOutput->learner->uuid);
        $this->assertEquals($sessionOutput->uuid, $enrollmentOutput->session->uuid);
    }
}
