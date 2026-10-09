<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit;

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
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;
use AgoraLearningPhp\Service\Auth\Ping;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\ClientCore\TokenHandler;
use AgoraLearningPhp\Service\Enrollment\Enrollment;
use AgoraLearningPhp\Service\Learner\Learner;
use AgoraLearningPhp\Service\Person\Person;
use AgoraLearningPhp\Service\Session\Session;
use AgoraLearningPhp\Service\Society\Society;
use AgoraLearningPhp\Service\Trainer\Trainer;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AgoraLearningClientTest extends TestCase
{
    private const BASE_URL = 'https://api.agoralearning.test';
    private const API_KEY = 'sk_test_dummy_key';

    /**
     * @var AgoraLearningClient
     */
    private $client;

    private $pingMock;
    private $personMock;
    private $trainerMock;
    private $learnerMock;
    private $societyMock;
    private $sessionMock;
    private $enrollmentMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pingMock = $this->createMock(Ping::class);
        $this->personMock = $this->createMock(Person::class);
        $this->trainerMock = $this->createMock(Trainer::class);
        $this->learnerMock = $this->createMock(Learner::class);
        $this->societyMock = $this->createMock(Society::class);
        $this->sessionMock = $this->createMock(Session::class);
        $this->enrollmentMock = $this->createMock(Enrollment::class);

        $this->client = new class (
            $this->pingMock,
            $this->personMock,
            $this->trainerMock,
            $this->learnerMock,
            $this->societyMock,
            $this->sessionMock,
            $this->enrollmentMock
        ) extends AgoraLearningClient {
            private $p;
            private $pe;
            private $t;
            private $l;
            private $so;
            private $se;
            private $en;

            public function __construct($p, $pe, $t, $l, $so, $se, $en)
            {
                // Volontairement aucun appel à parent::__construct() ici pour isoler les méthodes unitaires
                $this->p = $p;
                $this->pe = $pe;
                $this->t = $t;
                $this->l = $l;
                $this->so = $so;
                $this->se = $se;
                $this->en = $en;
            }

            protected function makePing(): Ping
            {
                return $this->p;
            }
            protected function makePerson(): Person
            {
                return $this->pe;
            }
            protected function makeTrainer(): Trainer
            {
                return $this->t;
            }
            protected function makeLearner(): Learner
            {
                return $this->l;
            }
            protected function makeSociety(): Society
            {
                return $this->so;
            }
            protected function makeSession(): Session
            {
                return $this->se;
            }
            protected function makeEnrollment(): Enrollment
            {
                return $this->en;
            }
        };
    }

    protected function tearDown(): void
    {
        $refUrls = new \ReflectionClass(ApiUrls::class);
        $urlProp = $refUrls->getProperty('baseUrl');
        if (\PHP_VERSION_ID < 80100) {
            $urlProp->setAccessible(true);
        }
        $urlProp->setValue(null, '');

        $refToken  = new \ReflectionClass(TokenHandler::class);
        $keyProp   = $refToken->getProperty('apiKey');
        if (\PHP_VERSION_ID < 80100) {
            $keyProp->setAccessible(true);
        }
        $keyProp->setValue(null, '');

        $tokenProp = $refToken->getProperty('token');
        if (\PHP_VERSION_ID < 80100) {
            $tokenProp->setAccessible(true);
        }
        $tokenProp->setValue(null, null);

        $expireProp = $refToken->getProperty('expireAt');
        if (\PHP_VERSION_ID < 80100) {
            $expireProp->setAccessible(true);
        }
        $expireProp->setValue(null, null);
    }

    public function testConstructorInitializesBaseUrl(): void
    {
        // Act
        new AgoraLearningClient(self::BASE_URL, self::API_KEY);

        // Assert
        $refUrls = new \ReflectionClass(ApiUrls::class);
        $urlProp = $refUrls->getProperty('baseUrl');
        if (\PHP_VERSION_ID < 80100) {
            $urlProp->setAccessible(true);
        }
        $this->assertSame(self::BASE_URL, $urlProp->getValue(null));
    }

    public function testConstructorInitializesApiKey(): void
    {
        // Act
        new AgoraLearningClient(self::BASE_URL, self::API_KEY);

        // Assert
        $refToken = new \ReflectionClass(TokenHandler::class);
        $keyProp  = $refToken->getProperty('apiKey');
        if (\PHP_VERSION_ID < 80100) {
            $keyProp->setAccessible(true);
        }
        $this->assertSame(self::API_KEY, $keyProp->getValue(null));
    }

    // ─── AUTH TESTS ──────────────────────────────────────────────────────────

    public function testPing(): void
    {
        // Arrange
        $responseMock = $this->createMock(ResponseInterface::class);
        $this->pingMock->expects($this->once())->method('ping')->willReturn($responseMock);

        // Act
        $result = $this->client->ping();

        // Assert
        $this->assertSame($responseMock, $result);
    }

    // ─── PERSON TESTS ────────────────────────────────────────────────────────

    public function testGetCollectionPerson(): void
    {
        // Arrange
        $expectedCollection = [];
        $this->personMock->expects($this->once())->method('getCollectionPerson')->willReturn($expectedCollection);

        // Act
        $result = $this->client->getCollectionPerson();

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function testGetPerson(): void
    {
        // Arrange
        $identifier = new AgoraId('user-uuid-123');
        $output = $this->createFinalDtoInstance(PersonOutput::class);
        $this->personMock->expects($this->once())->method('getPerson')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getPerson($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testGetPersonWithExternalId(): void
    {
        // Arrange
        $identifier = new ExternalId('Src', 'X-42');
        $output = $this->createFinalDtoInstance(PersonOutput::class);
        $this->personMock->expects($this->once())->method('getPerson')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getPerson($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testCreatePerson(): void
    {
        // Arrange
        $input  = $this->createFinalDtoInstance(PersonInput::class);
        $output = new CreatedOutput(new AgoraId('per_1'));
        $this->personMock->expects($this->once())->method('postPerson')->with($input)->willReturn($output);

        // Act
        $result = $this->client->createPerson($input);

        // Assert
        $this->assertSame($output, $result);
    }

    // ─── TRAINER TESTS ───────────────────────────────────────────────────────

    public function testGetCollectionTrainer(): void
    {
        // Arrange
        $expectedCollection = [];
        $this->trainerMock->expects($this->once())->method('getCollectionTrainer')->willReturn($expectedCollection);

        // Act
        $result = $this->client->getCollectionTrainer();

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function testGetTrainer(): void
    {
        // Arrange
        $identifier = new AgoraId('trainer-uuid-123');
        $output = $this->createFinalDtoInstance(TrainerOutput::class);
        $this->trainerMock->expects($this->once())->method('getTrainer')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getTrainer($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testGetTrainerWithExternalId(): void
    {
        // Arrange
        $identifier = new ExternalId('Src', 'X-42');
        $output = $this->createFinalDtoInstance(TrainerOutput::class);
        $this->trainerMock->expects($this->once())->method('getTrainer')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getTrainer($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testCreateTrainerEmployee(): void
    {
        // Arrange
        $input  = $this->createFinalDtoInstance(TrainerEmployeeInput::class);
        $output = new CreatedOutput(new AgoraId('per_1'));
        $this->trainerMock->expects($this->once())->method('postTrainerEmployee')->with($input)->willReturn($output);

        // Act
        $result = $this->client->createTrainerEmployee($input);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testCreateTrainerFree(): void
    {
        // Arrange
        $input  = $this->createFinalDtoInstance(TrainerFreeInput::class);
        $output = new CreatedOutput(new AgoraId('per_2'));
        $this->trainerMock->expects($this->once())->method('postTrainerFree')->with($input)->willReturn($output);

        // Act
        $result = $this->client->createTrainerFree($input);

        // Assert
        $this->assertSame($output, $result);
    }

    // ─── LEARNER TESTS ───────────────────────────────────────────────────────

    public function testGetCollectionLearner(): void
    {
        // Arrange
        $expectedCollection = [];
        $this->learnerMock->expects($this->once())->method('getCollectionLearner')->willReturn($expectedCollection);

        // Act
        $result = $this->client->getCollectionLearner();

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function testGetLearner(): void
    {
        // Arrange
        $identifier = new AgoraId('learner-uuid-123');
        $output = $this->createFinalDtoInstance(LearnerOutput::class);
        $this->learnerMock->expects($this->once())->method('getLearner')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getLearner($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testGetLearnerWithExternalId(): void
    {
        // Arrange
        $identifier = new ExternalId('Src', 'X-42');
        $output = $this->createFinalDtoInstance(LearnerOutput::class);
        $this->learnerMock->expects($this->once())->method('getLearner')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getLearner($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testCreateLearner(): void
    {
        // Arrange
        $input  = $this->createFinalDtoInstance(LearnerInput::class);
        $output = new CreatedOutput(new AgoraId('per_3'));
        $this->learnerMock->expects($this->once())->method('postLearner')->with($input)->willReturn($output);

        // Act
        $result = $this->client->createLearner($input);

        // Assert
        $this->assertSame($output, $result);
    }

    // ─── SOCIETY TESTS ───────────────────────────────────────────────────────

    public function testGetCollectionSociety(): void
    {
        // Arrange
        $expectedCollection = [];
        $this->societyMock->expects($this->once())->method('getCollectionSociety')->willReturn($expectedCollection);

        // Act
        $result = $this->client->getCollectionSociety();

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function testGetSociety(): void
    {
        // Arrange
        $identifier = new AgoraId('society-uuid-123');
        $output = $this->createFinalDtoInstance(SocietyOutput::class);
        $this->societyMock->expects($this->once())->method('getSociety')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getSociety($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testGetSocietyWithExternalId(): void
    {
        // Arrange
        $identifier = new ExternalId('Src', 'X-42');
        $output = $this->createFinalDtoInstance(SocietyOutput::class);
        $this->societyMock->expects($this->once())->method('getSociety')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getSociety($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testCreateSociety(): void
    {
        // Arrange
        $input  = $this->createFinalDtoInstance(SocietyInput::class);
        $output = new CreatedOutput(new AgoraId('soc_1'));
        $this->societyMock->expects($this->once())->method('postSociety')->with($input)->willReturn($output);

        // Act
        $result = $this->client->createSociety($input);

        // Assert
        $this->assertSame($output, $result);
    }

    // ─── SESSION TESTS ───────────────────────────────────────────────────────

    public function testGetCollectionSession(): void
    {
        // Arrange
        $expectedCollection = [];
        $this->sessionMock->expects($this->once())->method('getCollectionSession')->willReturn($expectedCollection);

        // Act
        $result = $this->client->getCollectionSession();

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function testGetSession(): void
    {
        // Arrange
        $identifier = new AgoraId('session-uuid-123');
        $output = $this->createFinalDtoInstance(SessionOutput::class);
        $this->sessionMock->expects($this->once())->method('getSession')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getSession($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testGetSessionWithExternalId(): void
    {
        // Arrange
        $identifier = new ExternalId('Src', 'S-42');
        $output = $this->createFinalDtoInstance(SessionOutput::class);
        $this->sessionMock->expects($this->once())->method('getSession')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getSession($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testCreateSession(): void
    {
        // Arrange
        $input  = $this->createFinalDtoInstance(SessionInput::class);
        $output = $this->createFinalDtoInstance(CreatedOutput::class);
        $this->sessionMock->expects($this->once())->method('postSession')->with($input)->willReturn($output);

        // Act
        $result = $this->client->createSession($input);

        // Assert
        $this->assertSame($output, $result);
    }

    // ─── ENROLLMENT TESTS ────────────────────────────────────────────────────

    public function testGetCollectionEnrollmentFromSession(): void
    {
        // Arrange
        $sessionIdentifier = new ExternalId('Src', 'S-789');
        $expectedCollection = [];
        $this->enrollmentMock->expects($this->once())
            ->method('getCollectionEnrollmentFromSession')
            ->with($sessionIdentifier)
            ->willReturn($expectedCollection);

        // Act
        $result = $this->client->getCollectionEnrollmentFromSession($sessionIdentifier);

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function testGetCollectionEnrollmentFromLearner(): void
    {
        // Arrange
        $learnerIdentifier = new ExternalId('Src', 'L-789');
        $expectedCollection = [];
        $this->enrollmentMock->expects($this->once())
            ->method('getCollectionEnrollmentFromLearner')
            ->with($learnerIdentifier)
            ->willReturn($expectedCollection);

        // Act
        $result = $this->client->getCollectionEnrollmentFromLearner($learnerIdentifier);

        // Assert
        $this->assertSame($expectedCollection, $result);
    }

    public function testGetEnrollment(): void
    {
        // Arrange
        $identifier = new AgoraId('enrollment-uuid-123');
        $output = $this->createFinalDtoInstance(EnrollmentOutput::class);
        $this->enrollmentMock->expects($this->once())->method('getEnrollment')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getEnrollment($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testGetEnrollmentWithExternalId(): void
    {
        // Arrange
        $identifier = new ExternalId('Src', 'X-42');
        $output = $this->createFinalDtoInstance(EnrollmentOutput::class);
        $this->enrollmentMock->expects($this->once())->method('getEnrollment')->with($identifier)->willReturn($output);

        // Act
        $result = $this->client->getEnrollment($identifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testGetEnrollmentFromSessionAndLearner(): void
    {
        // Arrange
        $sessionIdentifier = new AgoraId('session-999');
        $learnerIdentifier = new ExternalId('Src', 'L-999');
        $output      = $this->createFinalDtoInstance(EnrollmentOutput::class);
        $this->enrollmentMock->expects($this->once())
            ->method('getEnrollmentFromSessionAndLearner')
            ->with($sessionIdentifier, $learnerIdentifier)
            ->willReturn($output);

        // Act
        $result = $this->client->getEnrollmentFromSessionAndLearner($sessionIdentifier, $learnerIdentifier);

        // Assert
        $this->assertSame($output, $result);
    }

    public function testCreateEnrollment(): void
    {
        // Arrange
        $input  = $this->createFinalDtoInstance(EnrollmentInput::class);
        $output = new CreatedOutput(new AgoraId('enr_1'));
        $this->enrollmentMock->expects($this->once())->method('postEnrollment')->with($input)->willReturn($output);

        // Act
        $result = $this->client->createEnrollment($input);

        // Assert
        $this->assertSame($output, $result);
    }

    private function createFinalDtoInstance(string $className)
    {
        $reflection = new \ReflectionClass($className);
        return $reflection->newInstanceWithoutConstructor();
    }
}
