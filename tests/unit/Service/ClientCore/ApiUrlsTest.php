<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\ClientCore;

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
        $this->assertSame('/api/external', ApiUrls::PREFIX_API);
    }

    public function testApiVersionConstant(): void
    {
        $this->assertSame('/v1', ApiUrls::API_VERSION);
    }

    public function testDefaultBaseUrlConstant(): void
    {
        $this->assertSame('localhost/phoenix', ApiUrls::DEFAULT_BASE_URL);
    }

    public function testInitBaseUrlSetsUrl(): void
    {
        ApiUrls::initBaseUrl('https://myserver.example.com');
        $this->assertSame('https://myserver.example.com/api/external/v1/ping', ApiUrls::getPing());
    }

    public function testInitBaseUrlOnlySetOnce(): void
    {
        ApiUrls::initBaseUrl('https://first.example.com');
        ApiUrls::initBaseUrl('https://second.example.com');
        $this->assertStringContainsString('first.example.com', ApiUrls::getPing());
    }

    public function testDefaultBaseUrlWhenNotInitialized(): void
    {
        $this->assertStringStartsWith(ApiUrls::DEFAULT_BASE_URL, ApiUrls::getPing());
    }

    public function testGetPing(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::PING, ApiUrls::getPing());
    }

    public function testGetGetToken(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::GET_TOKEN, ApiUrls::getGetToken());
    }

    public function testGetCreateLearner(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::CREATE_LEARNER, ApiUrls::getCreateLearner());
    }

    public function testGetGetLearner(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'learner-uuid-123';
        $this->assertSame(self::BASE . ApiUrls::GET_LEARNER . $uuid, ApiUrls::getGetLearner($uuid));
    }

    public function testGetGetCollectionLearner(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_LEARNER, ApiUrls::getGetCollectionLearner());
    }

    public function testGetCreateTrainerEmployee(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::CREATE_TRAINER_EMPLOYEE, ApiUrls::getCreateTrainerEmployee());
    }

    public function testGetCreateTrainerFree(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::CREATE_TRAINER_FREE, ApiUrls::getCreateTrainerFree());
    }

    public function testGetGetTrainer(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'trainer-uuid-001';
        $this->assertSame(self::BASE . ApiUrls::GET_TRAINER . $uuid, ApiUrls::getGetTrainer($uuid));
    }

    public function testGetGetCollectionTrainer(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_TRAINER, ApiUrls::getGetCollectionTrainer());
    }

    public function testGetCreatePerson(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::CREATE_PERSON, ApiUrls::getCreatePerson());
    }

    public function testGetGetPerson(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'person-uuid-456';
        $this->assertSame(self::BASE . ApiUrls::GET_PERSON . $uuid, ApiUrls::getGetPerson($uuid));
    }

    public function testGetGetCollectionPerson(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_PERSON, ApiUrls::getGetCollectionPerson());
    }

    public function testGetCreateFixedSession(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::CREATE_FIXED_SESSION, ApiUrls::getCreateFixedSession());
    }

    public function testGetCreateOpenedSession(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::CREATE_OPEN_SESSION, ApiUrls::getCreateOpenedSession());
    }

    public function testGetGetSession(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'session-uuid-789';
        $this->assertSame(self::BASE . ApiUrls::GET_SESSION . $uuid, ApiUrls::getGetSession($uuid));
    }

    public function testGetGetCollectionSession(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_SESSION, ApiUrls::getGetCollectionSession());
    }

    public function testGetCreateSociety(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::CREATE_SOCIETY, ApiUrls::getCreateSociety());
    }

    public function testGetGetSociety(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'society-uuid-012';
        $this->assertSame(self::BASE . ApiUrls::GET_SOCIETY . $uuid, ApiUrls::getGetSociety($uuid));
    }

    public function testGetGetCollectionSociety(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::GET_COLLECTION_SOCIETY, ApiUrls::getGetCollectionSociety());
    }

    public function testGetCreateEnrollment(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $this->assertSame(self::BASE . ApiUrls::CREATE_ENROLLMENT, ApiUrls::getCreateEnrollment());
    }

    public function testGetGetEnrollment(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $uuid = 'enroll-uuid-345';
        $this->assertSame(self::BASE . ApiUrls::GET_ENROLLMENT . $uuid, ApiUrls::getGetEnrollment($uuid));
    }

    public function testGetGetEnrollmentFromSessionAndLearner(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $sessionId = 'sess-111';
        $learnerId = 'learn-222';
        $expected  = self::BASE . sprintf(ApiUrls::GET_ENROLLMENT_FROM_SESSION_AND_LEARNER, $sessionId, $learnerId);
        $this->assertSame($expected, ApiUrls::getGetEnrollmentFromSessionAndLearner($sessionId, $learnerId));
    }

    public function testGetGetCollectionEnrollmentFromSession(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $sessionId = 'sess-333';
        $expected  = self::BASE . sprintf(ApiUrls::GET_COLLECTION_ENROLLMENT_FROM_SESSION, $sessionId);
        $this->assertSame($expected, ApiUrls::getGetCollectionEnrollmentFromSession($sessionId));
    }

    public function testGetGetCollectionEnrollmentFromLearner(): void
    {
        ApiUrls::initBaseUrl(self::BASE);
        $learnerId = 'learn-444';
        $expected  = self::BASE . sprintf(ApiUrls::GET_COLLECTION_ENROLLMENT_FROM_LEARNER, $learnerId);
        $this->assertSame($expected, ApiUrls::getGetCollectionEnrollmentFromLearner($learnerId));
    }
}
