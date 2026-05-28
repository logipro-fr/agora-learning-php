<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\Enrollment;

use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\DTO\Output\Enrollment\EnrollmentOutput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Enrollment\Enrollment;
use AgoraLearningPhp\Tests\Service\ServiceTestCase;

class EnrollmentTest extends ServiceTestCase
{
    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    private function makePersonData(string $id = 'person-uuid-001'): array
    {
        return [
            'id'         => $id,
            'familyName' => 'Dupont',
            'givenName'  => 'Jean',
        ];
    }

    private function makeFixedSessionData(string $id = 'sess-uuid-001'): array
    {
        return [
            'id'              => $id,
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
            'id'              => $id,
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
            'id'                    => 'enroll-uuid-001',
            'learner'               => $this->makePersonData(),
            'session'               => $sessionData,
            'availabilityStartDate' => '2024-02-01T00:00:00+00:00',
            'availabilityEndDate'   => '2024-11-30T00:00:00+00:00',
        ];
    }

    private function makeEnrollment(HttpClient $httpClient): Enrollment
    {
        return new class($httpClient) extends Enrollment {
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
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeFixedSessionData()));

        $this->assertInstanceOf(EnrollmentOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeFixedSessionData()));

        $this->assertSame('enroll-uuid-001', $dto->uuid);
        $this->assertSame('person-uuid-001', $dto->learner->uuid);
        $this->assertSame('sess-uuid-001', $dto->session->uuid);
    }

    public function testConvertDataToDTOParsesAvailabilityDates(): void
    {
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeFixedSessionData()));

        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->availabilityStartDate);
        $this->assertSame('2024-02-01', $dto->availabilityStartDate->format('Y-m-d'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->availabilityEndDate);
        $this->assertSame('2024-11-30', $dto->availabilityEndDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOWithFixedSession(): void
    {
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeFixedSessionData()));

        $this->assertInstanceOf(FixedSessionOutput::class, $dto->session->sessionData);
    }

    public function testConvertDataToDTOWithOpenedSession(): void
    {
        $dto = Enrollment::convertDataToDTO($this->makeEnrollmentData($this->makeOpenedSessionData()));

        $this->assertInstanceOf(OpenedSessionOutput::class, $dto->session->sessionData);
        $this->assertSame(60, $dto->session->sessionData->accessDurationDays);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionEnrollmentFromSessionReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        $enrollment = $this->makeEnrollment($this->makeHttpClientWithResponse(200, '[]'));

        $result = $enrollment->getCollectionEnrollmentFromSession('sess-uuid-001');

        $this->assertSame([], $result);
    }

    public function testGetEnrollmentThrowsExceptionWhenApiReturns404(): void
    {
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Enrollment not found']))
        );

        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Enrollment not found');

        $enrollment->getEnrollment('missing-uuid');
    }

    public function testGetCollectionEnrollmentFromSessionReturnsDTOArray(): void
    {
        $row1       = array_merge($this->makeEnrollmentData($this->makeFixedSessionData('s1')), ['id' => 'e1']);
        $row2       = array_merge($this->makeEnrollmentData($this->makeOpenedSessionData('s2')), ['id' => 'e2']);
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode([$row1, $row2]))
        );

        $result = $enrollment->getCollectionEnrollmentFromSession('sess-uuid-001');

        $this->assertCount(2, $result);
        $this->assertInstanceOf(EnrollmentOutput::class, $result[0]);
        $this->assertSame('e1', $result[0]->uuid);
    }

    public function testGetCollectionEnrollmentFromLearnerReturnsDTOArray(): void
    {
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode([$this->makeEnrollmentData($this->makeFixedSessionData())]))
        );

        $result = $enrollment->getCollectionEnrollmentFromLearner('learner-uuid-001');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(EnrollmentOutput::class, $result[0]);
    }

    public function testGetEnrollmentReturnsSingleDTO(): void
    {
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeEnrollmentData($this->makeFixedSessionData())))
        );

        $result = $enrollment->getEnrollment('enroll-uuid-001');

        $this->assertInstanceOf(EnrollmentOutput::class, $result);
        $this->assertSame('enroll-uuid-001', $result->uuid);
    }

    public function testGetEnrollmentFromSessionAndLearner(): void
    {
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeEnrollmentData($this->makeFixedSessionData())))
        );

        $result = $enrollment->getEnrollmentFromSessionAndLearner('sess-uuid-001', 'learner-uuid-001');

        $this->assertInstanceOf(EnrollmentOutput::class, $result);
    }

    public function testPostEnrollment(): void
    {
        $enrollment = $this->makeEnrollment(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeEnrollmentData($this->makeFixedSessionData())))
        );
        $input = new EnrollmentInput('sess-uuid-001', 'learner-uuid-001', true);

        $result = $enrollment->postEnrollment($input);

        $this->assertInstanceOf(EnrollmentOutput::class, $result);
        $this->assertSame('enroll-uuid-001', $result->uuid);
    }
}
