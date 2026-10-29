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
        if (\PHP_VERSION_ID < 80100) {
            $prop->setAccessible(true);
        }
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

    public function testInitBaseUrlRemovesTrailingSlash(): void
    {
        // Act
        ApiUrls::initBaseUrl('https://myserver.example.com/');

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

    public function testGetGetSessionKeepsAgoraIdUnchanged(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetSession('ses_abc');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/sessions/ses_abc', $url);
    }

    public function testGetGetSessionEncodesColon(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetSession('Src:S-42');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/sessions/Src%3AS-42', $url);
    }

    /**
     * @dataProvider reservedCharactersProvider
     */
    public function testGetGetSessionEncodesReservedCharacters(string $identifier, string $expectedSegment): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetSession($identifier);

        // Assert
        $this->assertSame(self::BASE . ApiUrls::GET_SESSION . $expectedSegment, $url);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function reservedCharactersProvider(): array
    {
        return [
            'slash'    => ['Src:a/b', 'Src%3Aa%2Fb'],
            'question' => ['Src:a?b=1', 'Src%3Aa%3Fb%3D1'],
            'dièse'    => ['Src:a#b', 'Src%3Aa%23b'],
            'espace'   => ['Src:a b', 'Src%3Aa%20b'],
            'unicode'  => ['Src:é', 'Src%3A%C3%A9'],
        ];
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

    public function testGetDeleteEnrollment(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDeleteEnrollment('Src:E-42');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::DELETE_ENROLLMENT . 'Src%3AE-42', $url);
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

    /**
     * @dataProvider singleIdentifierUrlProvider
     */
    public function testSingleIdentifierUrlsEncodeExternalId(string $method, string $expectedPath): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::$method('Src:a/b');

        // Assert
        $this->assertSame(self::BASE . $expectedPath, $url);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public function singleIdentifierUrlProvider(): array
    {
        return [
            'person'                     => ['getGetPerson', '/api/external/v1/persons/Src%3Aa%2Fb'],
            'trainer'                    => ['getGetTrainer', '/api/external/v1/trainers/Src%3Aa%2Fb'],
            'learner'                    => ['getGetLearner', '/api/external/v1/learners/Src%3Aa%2Fb'],
            'society'                    => ['getGetSociety', '/api/external/v1/societies/Src%3Aa%2Fb'],
            'enrollment'                 => ['getGetEnrollment', '/api/external/v1/enrollments/Src%3Aa%2Fb'],
            'enrollments from session'   => ['getGetCollectionEnrollmentFromSession', '/api/external/v1/sessions/Src%3Aa%2Fb/enrollments'],
            'enrollments from learner'   => ['getGetCollectionEnrollmentFromLearner', '/api/external/v1/learners/Src%3Aa%2Fb/enrollments'],
        ];
    }

    public function testGetGetEnrollmentFromSessionAndLearnerEncodesBothIdentifiers(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getGetEnrollmentFromSessionAndLearner('Src:S-1', 'Src:a/b');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/sessions/Src%3AS-1/learners/Src%3Aa%2Fb/enrollments', $url);
    }

    public function testGetUpdatePersonEncodesIdentifier(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getUpdatePerson('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::UPDATE_PERSON . 'Src%3AX%201', $url);
    }

    public function testGetUpdateTrainerEmployeeEncodesIdentifier(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getUpdateTrainerEmployee('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::UPDATE_TRAINER_EMPLOYEE . 'Src%3AX%201', $url);
    }

    public function testGetUpdateTrainerFreeEncodesIdentifier(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getUpdateTrainerFree('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::UPDATE_TRAINER_FREE . 'Src%3AX%201', $url);
    }

    public function testGetUpdateLearnerEncodesIdentifier(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getUpdateLearner('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::UPDATE_LEARNER . 'Src%3AX%201', $url);
    }

    public function testGetUpdateFixedSessionEncodesIdentifier(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getUpdateFixedSession('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::UPDATE_FIXED_SESSION . 'Src%3AX%201', $url);
    }

    public function testGetUpdateOpenedSessionEncodesIdentifier(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getUpdateOpenedSession('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::UPDATE_OPEN_SESSION . 'Src%3AX%201', $url);
    }

    public function testGetUpdateSocietyEncodesIdentifier(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getUpdateSociety('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::UPDATE_SOCIETY . 'Src%3AX%201', $url);
    }

    public function testUpdateUrlsMatchApiRoutes(): void
    {
        // Assert
        $this->assertSame('/api/external/v1/persons/', ApiUrls::UPDATE_PERSON);
        $this->assertSame('/api/external/v1/trainers/employee/', ApiUrls::UPDATE_TRAINER_EMPLOYEE);
        $this->assertSame('/api/external/v1/trainers/free/', ApiUrls::UPDATE_TRAINER_FREE);
        $this->assertSame('/api/external/v1/learners/', ApiUrls::UPDATE_LEARNER);
        $this->assertSame('/api/external/v1/sessions/fixed/', ApiUrls::UPDATE_FIXED_SESSION);
        $this->assertSame('/api/external/v1/sessions/opened/', ApiUrls::UPDATE_OPEN_SESSION);
        $this->assertSame('/api/external/v1/societies/', ApiUrls::UPDATE_SOCIETY);
    }

    public function testGetDeletePerson(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDeletePerson('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::DELETE_PERSON . 'Src%3AX%201', $url);
    }

    public function testGetDeleteTrainer(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDeleteTrainer('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::DELETE_TRAINER . 'Src%3AX%201', $url);
    }

    public function testGetDeleteLearner(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDeleteLearner('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::DELETE_LEARNER . 'Src%3AX%201', $url);
    }

    public function testGetDeleteSession(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDeleteSession('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::DELETE_SESSION . 'Src%3AX%201', $url);
    }

    public function testGetDetachPerson(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDetachPerson('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/persons/Src%3AX%201/detach', $url);
    }

    public function testGetDetachTrainer(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDetachTrainer('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/trainers/Src%3AX%201/detach', $url);
    }

    public function testGetDetachSociety(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDetachSociety('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/societies/Src%3AX%201/detach', $url);
    }

    public function testGetDetachSession(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDetachSession('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/sessions/Src%3AX%201/detach', $url);
    }

    public function testGetDeleteSociety(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDeleteSociety('Src:X 1');

        // Assert
        $this->assertSame(self::BASE . ApiUrls::DELETE_SOCIETY . 'Src%3AX%201', $url);
    }

    public function testGetDeleteEnrollmentFromSessionAndLearner(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDeleteEnrollmentFromSessionAndLearner('Src:S-1', 'per_001');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/sessions/Src%3AS-1/learners/per_001/enrollments', $url);
    }

    public function testGetDeleteCollectionEnrollmentFromSession(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDeleteCollectionEnrollmentFromSession('Src:S-1');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/sessions/Src%3AS-1/enrollments', $url);
    }

    public function testGetDeleteCollectionEnrollmentFromLearner(): void
    {
        // Arrange
        ApiUrls::initBaseUrl(self::BASE);

        // Act
        $url = ApiUrls::getDeleteCollectionEnrollmentFromLearner('Src:L-1');

        // Assert
        $this->assertSame(self::BASE . '/api/external/v1/learners/Src%3AL-1/enrollments', $url);
    }

    public function testDeleteUrlsMatchApiRoutes(): void
    {
        // Assert
        $this->assertSame('/api/external/v1/persons/', ApiUrls::DELETE_PERSON);
        $this->assertSame('/api/external/v1/trainers/', ApiUrls::DELETE_TRAINER);
        $this->assertSame('/api/external/v1/learners/', ApiUrls::DELETE_LEARNER);
        $this->assertSame('/api/external/v1/sessions/', ApiUrls::DELETE_SESSION);
        $this->assertSame('/api/external/v1/societies/', ApiUrls::DELETE_SOCIETY);
        $this->assertSame('/api/external/v1/enrollments/', ApiUrls::DELETE_ENROLLMENT);
    }
}
