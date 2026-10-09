<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\mock;

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Enrollment\EnrollmentOutput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerEmployeeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerFreeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AgoraLearningClientMock extends AgoraLearningClient
{
    public function __construct(
        string $url,
        string $apiKey
    ) {
    }

    // Auth
    public function ping(): ResponseInterface
    {
        return new MockResponse();
    }

    // Person
    /**
     * @return array<int, PersonOutput>
     */
    public function getCollectionPerson(): array
    {
        return [
            0 => new PersonOutput(
                'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Dupont',
                'Jean',
                'jean.dupont@example.com',
                '0123456789',
                '3 rue de la Paix',
                '75001',
                'Paris',
                'France',
                Gender::GENDER_MALE,
                new \DateTimeImmutable('1985-06-15'),
                'Développeur'
            ),
            1 => new PersonOutput(
                'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Derivière',
                'Olivia',
                'olivia.deriviere@example.com',
                '0123456789',
                '3 rue de la Paix',
                '75001',
                'Paris',
                'France',
                Gender::GENDER_FEMALE,
                new \DateTimeImmutable('1985-06-15'),
                'Développeuse'
            )
        ];
    }

    public function getPerson(ApiObjectIdentifier $identifier): PersonOutput
    {
        return new PersonOutput(
            $uuid,
            'Dupont',
            'Jean',
            'jean.dupont@example.com',
            '0123456789',
            '3 rue de la Paix',
            '75001',
            'Paris',
            'France',
            Gender::GENDER_MALE,
            new \DateTimeImmutable('1985-06-15'),
            'Développeur'
        );
    }

    public function createPerson(PersonInput $personInput): CreatedOutput
    {
        return new CreatedOutput(
            new AgoraId('per_01XXXXXXXXXXXXXXXXXXXXXXXXX'),
            $personInput->externalIdentifier
        );
    }

    // Trainer
    /**
     * @return array<int, TrainerOutput>
     */
    public function getCollectionTrainer(): array
    {
        return [
            0 => new TrainerEmployeeOutput(
                'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Martin',
                'Sophie',
                'sophie.martin@example.com',
                true,
                Gender::GENDER_FEMALE,
                ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX'],
                '0123456789',
                '0612345678',
                '12 avenue des Formateurs',
                '69001',
                'Lyon',
                'France',
                new \DateTimeImmutable('1990-03-20'),
                'Formatrice senior',
                new SocietyOutput(
                    'soc_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Formation Excellence SAS'
                )
            ),
            1 => new TrainerFreeOutput(
                'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Bernard',
                'Pierre',
                'pierre.bernard@example.com',
                true,
                Gender::GENDER_MALE,
                [],
                '0123456789',
                '0612345678',
                '5 rue des Indépendants',
                '33000',
                'Bordeaux',
                'France',
                new \DateTimeImmutable('1982-11-08'),
                'Formateur indépendant',
                new SocietyOutput(
                    'soc_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Formation Excellence SAS'
                ),
                80.0,
                600.0,
                '12345678901234',
                'FR12345678901',
                '987654321',
                '5 rue des Indépendants',
                '33000',
                'Bordeaux',
                'France'
            )
        ];
    }

    public function getTrainer(ApiObjectIdentifier $identifier): TrainerOutput
    {
        return new TrainerEmployeeOutput(
            $uuid,
            'Martin',
            'Sophie',
            'sophie.martin@example.com',
            true,
            Gender::GENDER_FEMALE,
            ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX'],
            '0123456789',
            '0612345678',
            '12 avenue des Formateurs',
            '69001',
            'Lyon',
            'France',
            new \DateTimeImmutable('1990-03-20'),
            'Formatrice senior',
            new SocietyOutput(
                'soc_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Formation Excellence SAS'
            )
        );
    }

    public function createTrainerEmployee(TrainerEmployeeInput $trainerEmployeeInput): CreatedOutput
    {
        return new CreatedOutput(
            new AgoraId('per_01XXXXXXXXXXXXXXXXXXXXXXXXX'),
            $trainerEmployeeInput->externalIdentifier
        );
    }

    public function createTrainerFree(TrainerFreeInput $trainerFreeInput): CreatedOutput
    {
        return new CreatedOutput(
            new AgoraId('per_01XXXXXXXXXXXXXXXXXXXXXXXXX'),
            $trainerFreeInput->externalIdentifier
        );
    }

    // Learner
    /**
     * @return array<int, LearnerOutput>
     */
    public function getCollectionLearner(): array
    {
        return [
            0 => new LearnerOutput(
                'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'alice.leblanc',
                'Leblanc',
                'Alice',
                Gender::GENDER_FEMALE,
                'alice.leblanc.recovery@example.com',
                'alice.leblanc@example.com',
                '0123456789',
                '8 rue des Apprenants',
                '13001',
                'Marseille',
                'France',
                new \DateTimeImmutable('1995-07-22'),
                'Chargée de projet'
            ),
            1 => new LearnerOutput(
                'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                'matt.palin',
                'Palin',
                'Matt',
                Gender::GENDER_MALE,
                'matt.palin.recovery@example.com'
            )
        ];
    }

    public function getLearner(ApiObjectIdentifier $identifier): LearnerOutput
    {
        return new LearnerOutput(
            $uuid,
            'alice.leblanc',
            'Leblanc',
            'Alice',
            Gender::GENDER_FEMALE,
            'alice.leblanc.recovery@example.com',
            'alice.leblanc@example.com',
            '0123456789',
            '8 rue des Apprenants',
            '13001',
            'Marseille',
            'France',
            new \DateTimeImmutable('1995-07-22'),
            'Chargée de projet'
        );
    }

    public function createLearner(LearnerInput $learnerInput): CreatedOutput
    {
        return new CreatedOutput(
            new AgoraId('per_01XXXXXXXXXXXXXXXXXXXXXXXXX'),
            $learnerInput->externalIdentifier
        );
    }

    // Society
    /**
     * @return array<int, SocietyOutput>
     */
    public function getCollectionSociety(): array
    {
        return [
            0 => new SocietyOutput(
                'soc_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Formation Excellence SAS',
                '12345678901234',
                'SAS',
                'contact@formation-excellence.fr',
                '0145678901',
                '15 rue de la Formation',
                '75008',
                'Paris',
                'France',
                '15 rue de la Formation',
                '75008',
                'Paris',
                'France',
                new PersonOutput(
                    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Durand',
                    'Paul',
                    'paul.durand@example.com',
                    '0123456789',
                ),
                new PersonOutput(
                    'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Girard',
                    'Marie',
                    'marie.girard@example.com',
                    '0987654321'
                ),
                new PersonOutput(
                    'per_03XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Derivière',
                    'Olivia',
                    'olivia.deriviere@example.com',
                    '0123456789'
                )
            ),
            1 => new SocietyOutput(
                'soc_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Pourtous Formation SAS'
            )
        ];
    }

    public function getSociety(ApiObjectIdentifier $identifier): SocietyOutput
    {
        return new SocietyOutput(
            $uuid,
            'Formation Excellence SAS',
            '12345678901234',
            'SAS',
            'contact@formation-excellence.fr',
            '0145678901',
            '15 rue de la Formation',
            '75008',
            'Paris',
            'France',
            '15 rue de la Formation',
            '75008',
            'Paris',
            'France',
            new PersonOutput(
                'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Durand',
                'Paul',
                'paul.durand@example.com',
                '0123456789',
            ),
            new PersonOutput(
                'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Girard',
                'Marie',
                'marie.girard@example.com',
                '0987654321'
            ),
            new PersonOutput(
                'per_03XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Derivière',
                'Olivia',
                'olivia.deriviere@example.com',
                '0123456789'
            )
        );
    }

    public function createSociety(SocietyInput $societyInput): CreatedOutput
    {
        return new CreatedOutput(
            new AgoraId('soc_01XXXXXXXXXXXXXXXXXXXXXXXXX'),
            $societyInput->externalIdentifier
        );
    }

    // Session
    /**
     * @return array<int, SessionOutput>
     */
    public function getCollectionSession(): array
    {
        return [
            0 => new SessionOutput(
                'ses_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Formation PHP avancé ouverte',
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_E_LEARNING,
                new OpenedSessionOutput(
                    new \DateTimeImmutable('2026-09-01'),
                    new \DateTimeImmutable('2027-09-05'),
                    15
                ),
                false,
                true,
                true,
                1500.0,
                28800,
                'Formation intensive sur les bonnes pratiques PHP',
                null,
                new TrainerEmployeeOutput(
                    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Martin',
                    'Sophie',
                    'sophie.martin@example.com',
                    true,
                    Gender::GENDER_FEMALE,
                    ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
                ),
                new PersonOutput(
                    'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Derivière',
                    'Olivia'
                ),
                null,
                null,
                'https://example.com/survey/delayed'
            ),
            1 => new SessionOutput(
                'ses_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Formation PHP avancé',
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE,
                new FixedSessionOutput(
                    new \DateTimeImmutable('2026-09-01'),
                    new \DateTimeImmutable('2026-09-05')
                ),
                false,
                true,
                true,
                1500.0,
                28800,
                'Formation intensive sur les bonnes pratiques PHP',
                12,
                new TrainerEmployeeOutput(
                    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Martin',
                    'Sophie',
                    'sophie.martin@example.com',
                    true,
                    Gender::GENDER_FEMALE,
                    ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
                ),
                new PersonOutput(
                    'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Derivière',
                    'Olivia'
                ),
                'https://example.com/survey/pre',
                'https://example.com/survey/spot',
                'https://example.com/survey/delayed'
            )
        ];
    }

    public function getSession(ApiObjectIdentifier $identifier): SessionOutput
    {
        return new SessionOutput(
            $identifier->getId(),
            'Formation PHP avancé',
            SessionType::SESSION_TYPE_INTER,
            SessionMode::SESSION_MODE_FACE_TO_FACE,
            new FixedSessionOutput(
                new \DateTimeImmutable('2026-09-01'),
                new \DateTimeImmutable('2026-09-05')
            ),
            false,
            true,
            true,
            1500.0,
            28800,
            'Formation intensive sur les bonnes pratiques PHP',
            12,
            new TrainerEmployeeOutput(
                'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Martin',
                'Sophie',
                'sophie.martin@example.com',
                true,
                Gender::GENDER_FEMALE,
                ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
            ),
            new PersonOutput(
                'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Derivière',
                'Olivia'
            ),
            'https://example.com/survey/pre',
            'https://example.com/survey/spot',
            'https://example.com/survey/delayed'
        );
    }

    public function createSession(SessionInput $sessionInput): CreatedOutput
    {
        return new CreatedOutput(
            new AgoraId('ses_01XXXXXXXXXXXXXXXXXXXXXXXXX'),
            $sessionInput->externalIdentifier
        );
    }

    // Enrollment
    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromSession(ApiObjectIdentifier $sessionIdentifier): array
    {
        return [
            0 => new EnrollmentOutput(
                'enr_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                new LearnerOutput(
                    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'dupont.jean',
                    'Dupont',
                    'Jean',
                    Gender::GENDER_MALE,
                    'jean.dupont.recovery@example.com',
                    'jean.dupont@example.com',
                    '0123456789',
                    '3 rue de la Paix',
                    '75001',
                    'Paris',
                    'France',
                    new \DateTimeImmutable('1985-06-15'),
                    'Développeur'
                ),
                new SessionOutput(
                    $sessionIdentifier->getId(),
                    'Formation PHP avancé',
                    SessionType::SESSION_TYPE_INTER,
                    SessionMode::SESSION_MODE_FACE_TO_FACE,
                    new FixedSessionOutput(
                        new \DateTimeImmutable('2026-09-01'),
                        new \DateTimeImmutable('2026-09-05')
                    ),
                    false,
                    true,
                    true,
                    1500.0,
                    28800,
                    'Formation intensive sur les bonnes pratiques PHP',
                    12,
                    new TrainerEmployeeOutput(
                        'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                        'Martin',
                        'Sophie',
                        'sophie.martin@example.com',
                        true,
                        Gender::GENDER_FEMALE,
                        ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
                    ),
                    new PersonOutput(
                        'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                        'Derivière',
                        'Olivia'
                    ),
                    'https://example.com/survey/pre',
                    'https://example.com/survey/spot',
                    'https://example.com/survey/delayed'
                ),
                new \DateTimeImmutable('2026-09-01'),
                new \DateTimeImmutable('2026-09-05')
            ),
            1 => new EnrollmentOutput(
                'enr_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                new LearnerOutput(
                    'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'deriviere.olivia',
                    'Derivière',
                    'Olivia',
                    Gender::GENDER_FEMALE,
                    'olivia.deriviere.recover@example.com',
                    'olivia.deriviere@example.com',
                    '0123456789',
                    '3 rue de la Paix',
                    '75001',
                    'Paris',
                    'France',
                    new \DateTimeImmutable('1985-06-15'),
                    'Développeuse'
                ),
                new SessionOutput(
                    $sessionIdentifier->getId(),
                    'Formation PHP avancé',
                    SessionType::SESSION_TYPE_INTER,
                    SessionMode::SESSION_MODE_FACE_TO_FACE,
                    new FixedSessionOutput(
                        new \DateTimeImmutable('2026-09-01'),
                        new \DateTimeImmutable('2026-09-05')
                    ),
                    false,
                    true,
                    true,
                    1500.0,
                    28800,
                    'Formation intensive sur les bonnes pratiques PHP',
                    12,
                    new TrainerEmployeeOutput(
                        'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                        'Martin',
                        'Sophie',
                        'sophie.martin@example.com',
                        true,
                        Gender::GENDER_FEMALE,
                        ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
                    ),
                    new PersonOutput(
                        'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                        'Derivière',
                        'Olivia'
                    ),
                    'https://example.com/survey/pre',
                    'https://example.com/survey/spot',
                    'https://example.com/survey/delayed'
                ),
                new \DateTimeImmutable('2026-09-01'),
                new \DateTimeImmutable('2026-09-05')
            )
        ];
    }

    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromLearner(ApiObjectIdentifier $learnerIdentifier): array
    {
        return [
            0 => new EnrollmentOutput(
                'enr_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                new LearnerOutput(
                    $learnerIdentifier->getId(),
                    'dupont.jean',
                    'Dupont',
                    'Jean',
                    Gender::GENDER_MALE,
                    'jean.dupont.recovery@example.com',
                    'jean.dupont@example.com',
                    '0123456789',
                    '3 rue de la Paix',
                    '75001',
                    'Paris',
                    'France',
                    new \DateTimeImmutable('1985-06-15'),
                    'Développeur'
                ),
                new SessionOutput(
                    'ses_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Formation PHP avancé',
                    SessionType::SESSION_TYPE_INTER,
                    SessionMode::SESSION_MODE_FACE_TO_FACE,
                    new FixedSessionOutput(
                        new \DateTimeImmutable('2026-09-01'),
                        new \DateTimeImmutable('2026-09-05')
                    ),
                    false,
                    true,
                    true,
                    1500.0,
                    28800,
                    'Formation intensive sur les bonnes pratiques PHP',
                    12,
                    new TrainerEmployeeOutput(
                        'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                        'Martin',
                        'Sophie',
                        'sophie.martin@example.com',
                        true,
                        Gender::GENDER_FEMALE,
                        ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
                    ),
                    new PersonOutput(
                        'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                        'Derivière',
                        'Olivia'
                    ),
                    'https://example.com/survey/pre',
                    'https://example.com/survey/spot',
                    'https://example.com/survey/delayed'
                ),
                new \DateTimeImmutable('2026-09-01'),
                new \DateTimeImmutable('2026-09-05')
            ),
            1 => new EnrollmentOutput(
                'enr_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                new LearnerOutput(
                    $learnerIdentifier->getId(),
                    'dupont.jean',
                    'Dupont',
                    'Jean',
                    Gender::GENDER_MALE,
                    'jean.dupont.recovery@example.com',
                    'jean.dupont@example.com',
                    '0123456789',
                    '3 rue de la Paix',
                    '75001',
                    'Paris',
                    'France',
                    new \DateTimeImmutable('1985-06-15'),
                    'Développeur'
                ),
                new SessionOutput(
                    'ses_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Formation PHP avancé ouverte',
                    SessionType::SESSION_TYPE_INTER,
                    SessionMode::SESSION_MODE_E_LEARNING,
                    new OpenedSessionOutput(
                        new \DateTimeImmutable('2026-09-01'),
                        new \DateTimeImmutable('2027-09-05'),
                        15
                    ),
                    false,
                    true,
                    true,
                    1500.0,
                    28800,
                    'Formation intensive sur les bonnes pratiques PHP',
                    null,
                    new TrainerEmployeeOutput(
                        'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                        'Martin',
                        'Sophie',
                        'sophie.martin@example.com',
                        true,
                        Gender::GENDER_FEMALE,
                        ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
                    ),
                    new PersonOutput(
                        'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                        'Derivière',
                        'Olivia'
                    ),
                    null,
                    null,
                    'https://example.com/survey/delayed'
                ),
                new \DateTimeImmutable('2026-09-01'),
                new \DateTimeImmutable('2026-09-05')
            )
        ];
    }

    public function getEnrollment(ApiObjectIdentifier $identifier): EnrollmentOutput
    {
        return new EnrollmentOutput(
            $uuid,
            new LearnerOutput(
                'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'dupont.jean',
                'Dupont',
                'Jean',
                Gender::GENDER_MALE,
                'jean.dupont.recovery@example.com',
                'jean.dupont@example.com',
                '0123456789',
                '3 rue de la Paix',
                '75001',
                'Paris',
                'France',
                new \DateTimeImmutable('1985-06-15'),
                'Développeur'
            ),
            new SessionOutput(
                'ses_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Formation PHP avancé',
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE,
                new FixedSessionOutput(
                    new \DateTimeImmutable('2026-09-01'),
                    new \DateTimeImmutable('2026-09-05')
                ),
                false,
                true,
                true,
                1500.0,
                28800,
                'Formation intensive sur les bonnes pratiques PHP',
                12,
                new TrainerEmployeeOutput(
                    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Martin',
                    'Sophie',
                    'sophie.martin@example.com',
                    true,
                    Gender::GENDER_FEMALE,
                    ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
                ),
                new PersonOutput(
                    'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Derivière',
                    'Olivia'
                ),
                'https://example.com/survey/pre',
                'https://example.com/survey/spot',
                'https://example.com/survey/delayed'
            ),
            new \DateTimeImmutable('2026-09-01'),
            new \DateTimeImmutable('2026-09-05')
        );
    }

    public function getEnrollmentFromSessionAndLearner(
        ApiObjectIdentifier $sessionIdentifier,
        ApiObjectIdentifier $learnerIdentifier
    ): EnrollmentOutput {
        return new EnrollmentOutput(
            'enr_01XXXXXXXXXXXXXXXXXXXXXXXXX',
            new LearnerOutput(
                $learnerIdentifier->getId(),
                'dupont.jean',
                'Dupont',
                'Jean',
                Gender::GENDER_MALE,
                'jean.dupont.recovery@example.com',
                'jean.dupont@example.com',
                '0123456789',
                '3 rue de la Paix',
                '75001',
                'Paris',
                'France',
                new \DateTimeImmutable('1985-06-15'),
                'Développeur'
            ),
            new SessionOutput(
                $sessionIdentifier->getId(),
                'Formation PHP avancé',
                SessionType::SESSION_TYPE_INTER,
                SessionMode::SESSION_MODE_FACE_TO_FACE,
                new FixedSessionOutput(
                    new \DateTimeImmutable('2026-09-01'),
                    new \DateTimeImmutable('2026-09-05')
                ),
                false,
                true,
                true,
                1500.0,
                28800,
                'Formation intensive sur les bonnes pratiques PHP',
                12,
                new TrainerEmployeeOutput(
                    'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Martin',
                    'Sophie',
                    'sophie.martin@example.com',
                    true,
                    Gender::GENDER_FEMALE,
                    ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
                ),
                new PersonOutput(
                    'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                    'Derivière',
                    'Olivia'
                ),
                'https://example.com/survey/pre',
                'https://example.com/survey/spot',
                'https://example.com/survey/delayed'
            ),
            new \DateTimeImmutable('2026-09-01'),
            new \DateTimeImmutable('2026-09-05')
        );
    }

    public function createEnrollment(EnrollmentInput $enrollmentInput): CreatedOutput
    {
        return new CreatedOutput(
            new AgoraId('enr_01XXXXXXXXXXXXXXXXXXXXXXXXX'),
            $enrollmentInput->externalIdentifier
        );
    }
}
