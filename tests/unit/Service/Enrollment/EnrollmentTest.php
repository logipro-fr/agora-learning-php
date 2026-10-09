<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Enrollment;

use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Enrollment\EnrollmentOutput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Enrollment\Enrollment;
use AgoraLearningPhp\Tests\unit\Service\ServiceTestCase;

class EnrollmentTest extends ServiceTestCase
{
    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    private function makeLearnerData(string $id = 'person-uuid-001'): array
    {
        return [
            'agoraId'    => $id,
            'username' => 'jean.dupont',
            'familyName' => 'Dupont',
            'givenName'  => 'Jean',
            'email'  => 'jean.dupont@client.mail',
            'recoverEmail'  => 'jean.dupont@client.mail'
        ];
    }

    private function makeFixedSessionData(string $id = 'sess-uuid-001'): array
    {
        return [
            'agoraId'         => $id,
            'title'           => 'Formation PHP',
            'type'            => SessionType::SESSION_TYPE_INTER,
            'mode'            => SessionMode::SESSION_MODE_E_LEARNING,
            'manualDuration'  => false,
            'hasForum'        => true,
            'notifyMailForum' => true,
            'price'           => 0.0,
            'dataSessionFixed' => [
                'availabilityStartDate' => '2024-01-01T00:00:00+00:00',
                'availabilityEndDate'   => '2024-12-31T00:00:00+00:00',
            ],
        ];
    }

    private function makeOpenedSessionData(string $id = 'sess-uuid-002'): array
    {
        return [
            'agoraId'         => $id,
            'title'           => 'Formation Ouverte',
            'type'            => SessionType::SESSION_TYPE_INTER,
            'mode'            => SessionMode::SESSION_MODE_E_LEARNING,
            'manualDuration'  => false,
            'hasForum'        => true,
            'notifyMailForum' => true,
            'price'           => 0.0,
            'dataSessionOpened' => [
                'subscribeStartDate' => '2024-03-01T00:00:00+00:00',
                'subscribeEndDate'   => '2024-09-30T00:00:00+00:00',
                'accessDurationDays' => 60,
            ],
        ];
    }

    private function makeEnrollmentData(array $sessionData): array
    {
        return [
            'agoraId'               => 'enroll-uuid-001',
            'learner'               => $this->makeLearnerData(),
            'session'               => $sessionData,
            'availabilityStartDate' => '2024-02-01T00:00:00+00:00',
            'availabilityEndDate'   => '2024-11-30T00:00:00+00:00',
        ];
    }

    private function makeEnrollment(HttpClient $httpClient): Enrollment
    {
        return new class ($httpClient) extends Enrollment {
            public function __construct(HttpClient $client)
            {
                $this->httpClient = $client;
            }
        };
    }

    // -------------------------------------------------------------------------
    // convertDataToDTO — logique pure, sans réseau
    // -------------------------------------------------------------------------

    public function testConvertDataToDTOReturnsEnrollmentOutput(): void
    {
        // Act
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeFixedSessionData()));

        // Assert
        $this->assertInstanceOf(EnrollmentOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsExternalIdentifier(): void
    {
        // Arrange
        $data = array_merge($this->makeEnrollmentData($this->makeFixedSessionData()), [
            'externalIdentifier' => ['sourceName' => 'MonERP', 'id' => 'X-42'],
        ]);

        // Act
        $dto = Enrollment::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(ExternalId::class, $dto->externalIdentifier);
        $this->assertSame('MonERP:X-42', $dto->externalIdentifier->getId());
    }

    public function testConvertDataToDTOSetsExternalIdentifierToNullWhenAbsent(): void
    {
        // Act
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeFixedSessionData()));

        // Assert
        $this->assertNull($dto->externalIdentifier);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        // Act
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeFixedSessionData()));

        // Assert
        $this->assertSame('enroll-uuid-001', $dto->uuid);
        $this->assertSame('person-uuid-001', $dto->learner->uuid);
        $this->assertSame('sess-uuid-001', $dto->session->uuid);
    }

    public function testConvertDataToDTOParsesAvailabilityDates(): void
    {
        // Act
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeFixedSessionData()));

        // Assert
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->availabilityStartDate);
        $this->assertSame('2024-02-01', $dto->availabilityStartDate->format('Y-m-d'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->availabilityEndDate);
        $this->assertSame('2024-11-30', $dto->availabilityEndDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOWithFixedSession(): void
    {
        // Act
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeFixedSessionData()));

        // Assert
        $this->assertInstanceOf(FixedSessionOutput::class, $dto->session->sessionData);
    }

    public function testConvertDataToDTOWithOpenedSession(): void
    {
        // Act
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeOpenedSessionData()));

        // Assert
        $this->assertInstanceOf(OpenedSessionOutput::class, $dto->session->sessionData);
        $this->assertSame(60, $dto->session->sessionData->accessDurationDays);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionEnrollmentFromSessionReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        // Arrange
        $enrollment = $this->makeEnrollment($this->makeHttpClientWithResponse(200, '[]'));

        // Act
        $result = $enrollment->getCollectionEnrollmentFromSession(new AgoraId('sess-uuid-001'));

        // Assert
        $this->assertSame([], $result);
    }

    public function testGetCollectionEnrollmentFromSessionReturnsDTOArray(): void
    {
        // Arrange
        $row1       = array_merge($this->makeEnrollmentData($this->makeFixedSessionData('s1')), ['agoraId' => 'e1']);
        $row2       = array_merge($this->makeEnrollmentData($this->makeOpenedSessionData('s2')), ['agoraId' => 'e2']);
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode([$row1, $row2]))
        );

        // Act
        $result = $enrollment->getCollectionEnrollmentFromSession(new AgoraId('sess-uuid-001'));

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(EnrollmentOutput::class, $result[0]);
        $this->assertSame('e1', $result[0]->uuid);
    }

    public function testGetCollectionEnrollmentFromLearnerReturnsDTOArray(): void
    {
        // Arrange
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode([$this->makeEnrollmentData($this->makeFixedSessionData())]))
        );

        // Act
        $result = $enrollment->getCollectionEnrollmentFromLearner(new AgoraId('learner-uuid-001'));

        // Assert
        $this->assertCount(1, $result);
        $this->assertInstanceOf(EnrollmentOutput::class, $result[0]);
    }

    public function testGetEnrollmentReturnsSingleDTO(): void
    {
        // Arrange
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeEnrollmentData($this->makeFixedSessionData())))
        );

        // Act
        $result = $enrollment->getEnrollment(new AgoraId('enroll-uuid-001'));

        // Assert
        $this->assertInstanceOf(EnrollmentOutput::class, $result);
        $this->assertSame('enroll-uuid-001', $result->uuid);
    }

    public function testGetEnrollmentWithExternalIdCallsEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $enrollment = $this->makeEnrollment($this->makeHttpClientInspectingRequest(
            200,
            json_encode($this->makeEnrollmentData($this->makeFixedSessionData())),
            $method,
            $url
        ));

        // Act
        $result = $enrollment->getEnrollment(new ExternalId('Src', 'X-42'));

        // Assert
        $this->assertSame('GET', $method);
        $this->assertSame('https://api.test.local/api/external/v1/enrollments/Src%3AX-42', $url);
        $this->assertSame('enroll-uuid-001', $result->uuid);
    }

    public function testGetEnrollmentThrowsExceptionWhenApiReturns404(): void
    {
        // Arrange
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Enrollment not found']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Enrollment not found');

        // Act
        $enrollment->getEnrollment(new AgoraId('missing-uuid'));
    }

    public function testGetEnrollmentFromSessionAndLearner(): void
    {
        // Arrange
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeEnrollmentData($this->makeFixedSessionData())))
        );

        // Act
        $result = $enrollment->getEnrollmentFromSessionAndLearner(new AgoraId('sess-uuid-001'), new AgoraId('learner-uuid-001'));

        // Assert
        $this->assertInstanceOf(EnrollmentOutput::class, $result);
    }

    public function testGetEnrollmentFromSessionAndLearnerWithExternalIdsCallsEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $enrollment = $this->makeEnrollment($this->makeHttpClientInspectingRequest(
            200,
            json_encode($this->makeEnrollmentData($this->makeFixedSessionData())),
            $method,
            $url
        ));

        // Act
        $enrollment->getEnrollmentFromSessionAndLearner(new ExternalId('Src', 'S-42'), new ExternalId('Src', 'L-42'));

        // Assert
        $this->assertSame('GET', $method);
        $this->assertSame('https://api.test.local/api/external/v1/sessions/Src%3AS-42/learners/Src%3AL-42/enrollments', $url);
    }

    public function testGetCollectionEnrollmentFromSessionWithExternalIdCallsEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $enrollment = $this->makeEnrollment($this->makeHttpClientInspectingRequest(200, '[]', $method, $url));

        // Act
        $enrollment->getCollectionEnrollmentFromSession(new ExternalId('Src', 'S-42'));

        // Assert
        $this->assertSame('GET', $method);
        $this->assertSame('https://api.test.local/api/external/v1/sessions/Src%3AS-42/enrollments', $url);
    }

    public function testGetCollectionEnrollmentFromLearnerWithExternalIdCallsEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $enrollment = $this->makeEnrollment($this->makeHttpClientInspectingRequest(200, '[]', $method, $url));

        // Act
        $enrollment->getCollectionEnrollmentFromLearner(new ExternalId('Src', 'L-42'));

        // Assert
        $this->assertSame('GET', $method);
        $this->assertSame('https://api.test.local/api/external/v1/learners/Src%3AL-42/enrollments', $url);
    }

    public function testPostEnrollment(): void
    {
        // Arrange
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode(['agoraId' => 'enr_new_001', 'sourceName' => 'Test', 'externalId' => 'E-1']))
        );
        $input = new EnrollmentInput(new ExternalId('Test', 'E-1'), 'sess-uuid-001', 'learner-uuid-001', true);

        // Act
        $result = $enrollment->postEnrollment($input);

        // Assert
        $this->assertInstanceOf(CreatedOutput::class, $result);
        $this->assertSame('enr_new_001', $result->agoraId->getId());
    }

    public function testPostEnrollmentSendsAllRequiredFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $enrollment   = $this->makeEnrollment(
            $this->makeHttpClientInspectingBody(
                200,
                json_encode(['agoraId' => 'enr_new_001', 'sourceName' => 'Test', 'externalId' => 'E-1']),
                $capturedBody
            )
        );

        // Act
        $enrollment->postEnrollment(new EnrollmentInput(new ExternalId('Test', 'E-2'), 'session-abc', 'learner-xyz', true));

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame(['sourceName' => 'Test', 'id' => 'E-2'], $body['externalIdentifier']);
        $this->assertSame('session-abc', $body['sessionId']);
        $this->assertSame('learner-xyz', $body['learnerId']);
        $this->assertTrue($body['sendingInvite']);
    }

    public function testGetCollectionEnrollmentFromSessionThrowsExceptionWithDetailOnError(): void
    {
        // Arrange
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Session not found']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Session not found');

        // Act
        $enrollment->getCollectionEnrollmentFromSession(new AgoraId('missing-uuid'));
    }

    public function testGetCollectionEnrollmentFromSessionThrowsDefaultMessageWithoutDetail(): void
    {
        // Arrange
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(500, json_encode([]))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('An unexpected error occured');

        // Act
        $enrollment->getCollectionEnrollmentFromSession(new AgoraId('any-uuid'));
    }
}
