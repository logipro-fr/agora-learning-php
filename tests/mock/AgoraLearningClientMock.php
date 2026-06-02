<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\mock;

use AgoraLearningPhp\AgoraLearningClient;
use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
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
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AgoraLearningClientMock extends AgoraLearningClient
{
    public function __construct(
        string $url,
        string $apiKey
    ) {}

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

    public function getPerson(string $uuid): PersonOutput
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

    public function createPerson(PersonInput $personInput): PersonOutput
    {
        return new PersonOutput(
            'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
            $personInput->familyName,
            $personInput->givenName,
            $personInput->email,
            $personInput->telephone,
            $personInput->addressStreet,
            $personInput->addressPostcode,
            $personInput->addressLocality,
            $personInput->addressCountry,
            $personInput->gender,
            $personInput->birthDate,
            $personInput->jobTitle
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

    public function getTrainer(string $uuid): TrainerOutput
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

    public function createTrainerEmployee(TrainerEmployeeInput $trainerEmployeeInput): TrainerOutput
    {
        return new TrainerEmployeeOutput(
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
        );
    }

    public function createTrainerFree(TrainerFreeInput $trainerFreeInput): TrainerOutput
    {
        return new TrainerFreeOutput(
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

    public function getLearner(string $uuid): LearnerOutput
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

    public function createLearner(LearnerInput $learnerInput): LearnerOutput
    {
        return new LearnerOutput(
            'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
            'user.name',
            $learnerInput->familyName,
            $learnerInput->givenName,
            $learnerInput->gender,
            $learnerInput->recoverEmail,
            $learnerInput->email,
            $learnerInput->telephone,
            $learnerInput->addressStreet,
            $learnerInput->addressPostcode,
            $learnerInput->addressLocality,
            $learnerInput->addressCountry,
            $learnerInput->birthDate,
            $learnerInput->jobTitle
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

    public function getSociety(string $uuid): SocietyOutput
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

    public function createSociety(SocietyInput $societyInput): SocietyOutput
    {
        return new SocietyOutput(
            'soc_01XXXXXXXXXXXXXXXXXXXXXXXXX',
            $societyInput->name,
            $societyInput->siret,
            $societyInput->legalStatus,
            $societyInput->email,
            $societyInput->telephone,
            $societyInput->addressStreetHeadOffice,
            $societyInput->addressPostcodeHeadOffice,
            $societyInput->addressLocalityHeadOffice,
            $societyInput->addressCountryHeadOffice,
            $societyInput->addressStreetInvoicing,
            $societyInput->addressPostcodeInvoicing,
            $societyInput->addressLocalityInvoicing,
            $societyInput->addressCountryInvoicing,
            $societyInput->legalPerson,
            $societyInput->administrativePerson,
            $societyInput->rhPerson
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

    public function getSession(string $uuid): SessionOutput
    {
        return new SessionOutput(
            $uuid,
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

    public function createSession(SessionInput $sessionInput): SessionOutput
    {
        $sessionData = $sessionInput->sessionData;
        if ($sessionData instanceof FixedSessionInput) {
            $type = $sessionData->type;
            $mode = $sessionData->mode;
            $dateData = new FixedSessionOutput(
                $sessionData->availabilityStartDate,
                $sessionData->availabilityEndDate
            );
        } elseif ($sessionData instanceof OpenedSessionInput) {
            $type = SessionType::SESSION_TYPE_INTER;
            $mode = SessionMode::SESSION_MODE_E_LEARNING;
            $dateData = new OpenedSessionOutput(
                $sessionData->subscribeStartDate,
                $sessionData->subscribeEndDate,
                $sessionData->accessDurationDays
            );
        } else {
            throw new \Exception('Wrong SessionInput');
        }

        $trainer = null;
        if ($sessionInput->pedagogicalTrainer) {
            $trainer = new TrainerEmployeeOutput(
                'per_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Martin',
                'Sophie',
                'sophie.martin@example.com',
                true,
                Gender::GENDER_FEMALE,
                ['ses_01XXXXXXXXXXXXXXXXXXXXXXXXX']
            );
        }

        $administrativePerson = null;
        if ($sessionInput->administrativePerson) {
            $administrativePerson = new PersonOutput(
                'per_02XXXXXXXXXXXXXXXXXXXXXXXXX',
                'Derivière',
                'Olivia'
            );
        }

        return new SessionOutput(
            'ses_01XXXXXXXXXXXXXXXXXXXXXXXXX',
            $sessionInput->title,
            $type,
            $mode,
            $dateData,
            $sessionInput->manualDuration,
            $sessionInput->hasForum,
            $sessionInput->notifyMailForum,
            $sessionInput->price,
            $sessionInput->durationInSeconds,
            $sessionInput->description,
            $sessionInput->maxPlaces,
            $trainer,
            $administrativePerson,
            $sessionInput->urlPreTrainingSurvey,
            $sessionInput->urlOnTheSpotSurvey,
            $sessionInput->urlDelayedSurvey
        );
    }

    // Enrollment
    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromSession(string $sessionUuid): array
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
                    $sessionUuid,
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
                    $sessionUuid,
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
    public function getCollectionEnrollmentFromLearner(string $learnerUuid): array
    {
        return [
            0 => new EnrollmentOutput(
                'enr_01XXXXXXXXXXXXXXXXXXXXXXXXX',
                new LearnerOutput(
                    $learnerUuid,
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
                    $learnerUuid,
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

    public function getEnrollment(string $uuid): EnrollmentOutput
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

    public function getEnrollmentFromSessionAndLearner(string $sessionUuid, string $learnerUuid): EnrollmentOutput
    {
        return new EnrollmentOutput(
            'enr_01XXXXXXXXXXXXXXXXXXXXXXXXX',
            new LearnerOutput(
                $learnerUuid,
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
                $sessionUuid,
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

    public function createEnrollment(EnrollmentInput $enrollmentInput): EnrollmentOutput
    {
        return new EnrollmentOutput(
            'enr_01XXXXXXXXXXXXXXXXXXXXXXXXX',
            new LearnerOutput(
                $enrollmentInput->learnerUuid,
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
                $enrollmentInput->sessionUuid,
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
}
