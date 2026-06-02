<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\ClientCore;

use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use PHPUnit\Framework\TestCase;

class ApiUrlsTest extends TestCase
{
    private const BASE = 'https://api.example.com';

    protected function setUp(): void
    {
        $this->resetBaseUrl();
    }

    protected function tearDown(): void
    {
        $this->resetBaseUrl();
    }

    private function resetBaseUrl(): void
    {
        $reflection = new \ReflectionClass(ApiUrls::class);
        $prop = $reflection->getProperty('baseUrl');
        $prop->setAccessible(true);
        $prop->setValue(null, '');
    }

    public function testPrefixApiConstant(): void
    {
        // Act + Assert
        $this->assertSame('/api/external', ApiUrls::PREFIX_API);
    }

    public function testApiVersionConstant(): void
    {
        // Act + Assert
        $this->assertSame('/v1', ApiUrls::API_VERSION);
    }

    public function testDefaultBaseUrlConstant(): void
    {
        // Act + Assert
        $this->assertSame('localhost/phoenix', ApiUrls::DEFAULT_BASE_URL);
    }

    public function testInitBaseUrlSetsUrl(): void
    {
        // Act
        ApiUrls::initBaseUrl('https://myserver.example.com');

        // Assert
        $this->assertSame('https://myserver.example.com/api/external/v1/ping', ApiUrls::getPing());
    }

    public function testInitBaseUrlOnlySetOnce(): void
    {
        // Arrange
        ApiUrls::initBaseUrl('https://first.example.com');

        // Act
        ApiUrls::initBaseUrl('https://second.example.com');

        // Assert
        $this->assertStringContainsString('first.example.com', ApiUrls::getPing());
    }

    public function testDefaultBaseUrlWhenNotInitialized(): void
    {
        // Act + Assert
        $this->assertStringStartsWith(ApiUrls::DEFAULT_BASE_URL, ApiUrls::getPing());
    }

    public function testGetPing(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getPing();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::PING, $url);
    }

    public function testGetGetToken(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetToken();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_TOKEN, $url);
    }

    public function testGetCreateLearner(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getCreateLearner();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::CREATE_LEARNER, $url);
    }

    public function testGetGetLearner(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'learner-uuid-123';

        // Act
        $url = ApiUrls::getGetLearner($uuid);

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_LEARNER . $uuid, $url);
    }

    public function testGetGetCollectionLearner(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetCollectionLearner();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_LEARNER, $url);
    }

    public function testGetCreateTrainerEmployee(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getCreateTrainerEmployee();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::CREATE_TRAINER_EMPLOYEE, $url);
    }

    public function testGetCreateTrainerFree(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getCreateTrainerFree();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::CREATE_TRAINER_FREE, $url);
    }

    public function testGetGetTrainer(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'trainer-uuid-001';

        // Act
        $url = ApiUrls::getGetTrainer($uuid);

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_TRAINER . $uuid, $url);
    }

    public function testGetGetCollectionTrainer(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetCollectionTrainer();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_TRAINER, $url);
    }

    public function testGetCreatePerson(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getCreatePerson();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::CREATE_PERSON, $url);
    }

    public function testGetGetPerson(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'person-uuid-456';

        // Act
        $url = ApiUrls::getGetPerson($uuid);

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_PERSON . $uuid, $url);
    }

    public function testGetGetCollectionPerson(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetCollectionPerson();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_PERSON, $url);
    }

    public function testGetCreateFixedSession(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getCreateFixedSession();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::CREATE_FIXED_SESSION, $url);
    }

    public function testGetCreateOpenedSession(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getCreateOpenedSession();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::CREATE_OPEN_SESSION, $url);
    }

    public function testGetGetSession(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'session-uuid-789';

        // Act
        $url = ApiUrls::getGetSession($uuid);

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_SESSION . $uuid, $url);
    }

    public function testGetGetCollectionSession(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetCollectionSession();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_SESSION, $url);
    }

    public function testGetCreateSociety(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getCreateSociety();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::CREATE_SOCIETY, $url);
    }

    public function testGetGetSociety(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'society-uuid-012';

        // Act
        $url = ApiUrls::getGetSociety($uuid);

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_SOCIETY . $uuid, $url);
    }

    public function testGetGetCollectionSociety(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetCollectionSociety();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_SOCIETY, $url);
    }

    public function testGetCreateEnrollment(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getCreateEnrollment();

        // Assert
        $this->assertSame(self::BASE . ApiUrls::CREATE_ENROLLMENT, $url);
    }

    public function testGetGetEnrollment(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'enroll-uuid-345';

        // Act
        $url = ApiUrls::getGetEnrollment($uuid);

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_ENROLLMENT . $uuid, $url);
    }

    public function testGetGetEnrollmentFromSessionAndLearner(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);
        $sessionId = 'sess-111';
        $learnerId = 'learn-222';
        $expected  = self::BASE . sprintf(ApiUrls::GET_ENROLLMENT_FROM_SESSION_AND_LEARNER, $sessionId, $learnerId);

        // Act
        $url = ApiUrls::getGetEnrollmentFromSessionAndLearner($sessionId, $learnerId);

        // Assert
        $this->assertSame($expected, $url);
    }

    public function testGetGetCollectionEnrollmentFromSession(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);
        $sessionId = 'sess-333';
        $expected  = self::BASE . sprintf(ApiUrls::GET_COLLECTION_ENROLLMENT_FROM_SESSION, $sessionId);

        // Act
        $url = ApiUrls::getGetCollectionEnrollmentFromSession($sessionId);

        // Assert
        $this->assertSame($expected, $url);
    }

    public function testGetGetCollectionEnrollmentFromLearner(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);
        $learnerId = 'learn-444';
        $expected  = self::BASE . sprintf(ApiUrls::GET_COLLECTION_ENROLLMENT_FROM_LEARNER, $learnerId);

        // Act
        $url = ApiUrls::getGetCollectionEnrollmentFromLearner($learnerId);

        // Assert
        $this->assertSame($expected, $url);
    }
}
