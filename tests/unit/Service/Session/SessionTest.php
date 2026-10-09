<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Session;

use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Enum\TrainerStatut;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Session\Session;
use AgoraLearningPhp\Tests\unit\Service\ServiceTestCase;

class SessionTest extends ServiceTestCase
{
    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    private function makeBaseSessionData(): array
    {
        return [
            'agoraId'         => 'sess-uuid-001',
            'externalIdentifier' => ['sourceName' => 'Test', 'id' => 'S-1'],
            'title'           => 'Formation PHP',
            'type'            => SessionType::SESSION_TYPE_INTER,
            'mode'            => SessionMode::SESSION_MODE_E_LEARNING,
            'manualDuration'  => false,
            'hasForum'        => true,
            'notifyMailForum' => true,
            'price'           => 0.0,
            'pedagogicalTrainer' => $this->makeTrainerData(),
            'administrativePerson' => $this->makePersonData()
        ];
    }

    private function makeFixedSessionData(): array
    {
        return [
            'dataSessionFixed' => [
                'availabilityStartDate' => '2024-01-01T00:00:00+00:00',
                'availabilityEndDate'   => '2024-12-31T00:00:00+00:00',
            ],
        ];
    }

    private function makeOpenedSessionData(): array
    {
        return [
            'dataSessionOpened' => [
                'subscribeStartDate' => '2024-03-01T00:00:00+00:00',
                'subscribeEndDate'   => '2024-09-30T00:00:00+00:00',
                'accessDurationDays' => 90,
            ],
        ];
    }

    private function makeSession(HttpClient $httpClient): Session
    {
        return new class ($httpClient) extends Session {
            public function __construct(HttpClient $client)
            {
                $this->httpClient = $client;
            }
        };
    }

    private function makePersonData(): array
    {
        return [
            'agoraId'    => 'person-uuid-001',
            'familyName' => 'Doe',
            'givenName'  => 'John',
            'email' => 'john.doe@example.net'
        ];
    }

    private function makeTrainerData(): array
    {
        return [
            'agoraId'                   => 'trainer-emp-001',
            'familyName'                => 'Dupont',
            'givenName'                 => 'Jean',
            'email'                     => 'jean@example.com',
            'visibleInformation'        => true,
            'gender'                    => Gender::GENDER_NA,
            'educationalManagerSessions' => [],
            'statut'                    => TrainerStatut::STATUT_EMPLOYE,
        ];
    }

    // -------------------------------------------------------------------------
    // convertDataToDTO — logique pure, sans réseau
    // -------------------------------------------------------------------------

    public function testConvertDataToDTOReturnsSessionOutput(): void
    {
        // Act
        $dto = Session::convertDataToDTO(
            array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())
        );

        // Assert
        $this->assertInstanceOf(SessionOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        // Act
        $dto = Session::convertDataToDTO(
            array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())
        );

        // Assert
        $this->assertSame('sess-uuid-001', $dto->uuid);
        $this->assertNotNull($dto->externalIdentifier);
        $this->assertSame('Test:S-1', $dto->externalIdentifier->getId());
        $this->assertSame('Formation PHP', $dto->title);
        $this->assertSame(SessionType::SESSION_TYPE_INTER, $dto->type);
        $this->assertSame(SessionMode::SESSION_MODE_E_LEARNING, $dto->mode);
        $this->assertFalse($dto->manualDuration);
        $this->assertTrue($dto->hasForum);
    }

    public function testConvertDataToDTOReturnsFixedSessionData(): void
    {
        // Act
        $dto = Session::convertDataToDTO(
            array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())
        );

        // Assert
        $this->assertInstanceOf(FixedSessionOutput::class, $dto->sessionData);
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->sessionData->availabilityStartDate);
        $this->assertSame('2024-01-01', $dto->sessionData->availabilityStartDate->format('Y-m-d'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->sessionData->availabilityEndDate);
        $this->assertSame('2024-12-31', $dto->sessionData->availabilityEndDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOReturnsOpenedSessionData(): void
    {
        // Act
        $dto = Session::convertDataToDTO(
            array_merge($this->makeBaseSessionData(), $this->makeOpenedSessionData())
        );

        // Assert
        $this->assertInstanceOf(OpenedSessionOutput::class, $dto->sessionData);
        $this->assertSame(90, $dto->sessionData->accessDurationDays);
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->sessionData->subscribeStartDate);
        $this->assertSame('2024-03-01', $dto->sessionData->subscribeStartDate->format('Y-m-d'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->sessionData->subscribeEndDate);
        $this->assertSame('2024-09-30', $dto->sessionData->subscribeEndDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOMapsOptionalFields(): void
    {
        // Arrange
        $data = array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData(), [
            'duration'             => 7200,
            'description'          => 'Une description',
            'maxPlaces'            => 30,
            'urlPreTrainingSurvey' => 'http://pre.example.com',
            'urlOnTheSpotSurvey'   => 'http://spot.example.com',
            'urlDelayedSurvey'     => 'http://delayed.example.com'
        ]);

        // Act
        $dto = Session::convertDataToDTO($data);

        // Assert
        $this->assertSame(7200, $dto->duration);
        $this->assertSame('Une description', $dto->description);
        $this->assertSame(30, $dto->maxPlaces);
        $this->assertSame('http://pre.example.com', $dto->urlPreTrainingSurvey);
        $this->assertSame('http://spot.example.com', $dto->urlOnTheSpotSurvey);
        $this->assertSame('http://delayed.example.com', $dto->urlDelayedSurvey);
    }

    public function testConvertDataToDTOThrowsExceptionWhenNoSessionType(): void
    {
        // Assert
        $this->expectException(\Exception::class);

        // Act
        Session::convertDataToDTO($this->makeBaseSessionData());
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionSessionReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        // Arrange
        $session = $this->makeSession($this->makeHttpClientWithResponse(200, '[]'));

        // Act
        $result = $session->getCollectionSession();

        // Assert
        $this->assertSame([], $result);
    }

    public function testGetCollectionSessionReturnsDTOArray(): void
    {
        // Arrange
        $row1    = array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData(), ['agoraId' => 's1']);
        $row2    = array_merge($this->makeBaseSessionData(), $this->makeOpenedSessionData(), ['agoraId' => 's2']);
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(200, json_encode([$row1, $row2]))
        );

        // Act
        $result = $session->getCollectionSession();

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(SessionOutput::class, $result[0]);
        $this->assertSame('s1', $result[0]->uuid);
    }

    public function testGetSessionReturnsSingleDTO(): void
    {
        // Arrange
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(200, json_encode(
                array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())
            ))
        );

        // Act
        $result = $session->getSession(new AgoraId('sess-uuid-001'));

        // Assert
        $this->assertInstanceOf(SessionOutput::class, $result);
        $this->assertSame('sess-uuid-001', $result->uuid);
    }

    public function testGetSessionWithAgoraIdCallsAgoraUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $session = $this->makeSession($this->makeHttpClientInspectingRequest(
            200,
            json_encode(array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())),
            $method,
            $url
        ));

        // Act
        $result = $session->getSession(new AgoraId('sess-uuid-001'));

        // Assert
        $this->assertSame('GET', $method);
        $this->assertStringEndsWith('/sessions/sess-uuid-001', $url);
        $this->assertSame('sess-uuid-001', $result->uuid);
    }

    public function testGetSessionWithExternalIdCallsEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $session = $this->makeSession($this->makeHttpClientInspectingRequest(
            200,
            json_encode(array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())),
            $method,
            $url
        ));

        // Act
        $result = $session->getSession(new ExternalId('Src', 'S-42'));

        // Assert
        $this->assertSame('GET', $method);
        $this->assertSame('https://api.test.local/api/external/v1/sessions/Src%3AS-42', $url);
        $this->assertSame('sess-uuid-001', $result->uuid);
    }

    /**
     * @dataProvider invalidIdentifierProvider
     *
     * @param mixed $identifier
     */
    public function testGetSessionWithInvalidTypeIsRejected($identifier): void
    {
        // Arrange
        $session = $this->makeSession($this->makeHttpClientWithResponse(200, '{}'));

        // Assert
        $this->expectException(\TypeError::class);

        // Act
        $session->getSession($identifier);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public function invalidIdentifierProvider(): array
    {
        return [
            'string' => ['sess-uuid-001'],
            'int'    => [42],
            'null'   => [null],
            'array'  => [['Src', 'S-42']],
            'objet'  => [new \stdClass()],
        ];
    }

    public function testGetSessionThrowsWithHttpStatusCodeOnError(): void
    {
        // Arrange
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Session not found']))
        );

        // Act
        try {
            $session->getSession(new ExternalId('Src', 'S-42'));
            $this->fail('AgoraLearningClientException expected');
        } catch (AgoraLearningClientException $e) {
            // Assert
            $this->assertSame('Session not found', $e->getMessage());
            $this->assertSame(404, $e->getCode());
        }
    }

    public function testGetCollectionSessionThrowsWithHttpStatusCodeOnError(): void
    {
        // Arrange
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(400, json_encode(['detail' => 'Bad request']))
        );

        // Act
        try {
            $session->getCollectionSession();
            $this->fail('AgoraLearningClientException expected');
        } catch (AgoraLearningClientException $e) {
            // Assert
            $this->assertSame(400, $e->getCode());
        }
    }

    public function testPostSessionWithFixedSessionInput(): void
    {
        // Arrange
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(201, json_encode(
                ['agoraId' => 'ses_new_001', 'sourceName' => 'Test', 'externalId' => 'S-1']
            ))
        );
        $fixedData    = new FixedSessionInput(
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-12-31'),
            SessionType::SESSION_TYPE_INTER,
            SessionMode::SESSION_MODE_FACE_TO_FACE
        );
        $sessionInput = new SessionInput(new ExternalId('Test', 'S-1'), 'Formation PHP', $fixedData);

        // Act
        $result = $session->postSession($sessionInput);

        // Assert
        $this->assertInstanceOf(CreatedOutput::class, $result);
        $this->assertSame('ses_new_001', $result->agoraId->getId());
        $this->assertNotNull($result->externalId);
        $this->assertSame('Test:S-1', $result->externalId->getId());
    }

    public function testPostSessionWithOpenedSessionInput(): void
    {
        // Arrange
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(201, json_encode(
                ['agoraId' => 'ses_new_002', 'sourceName' => 'Test', 'externalId' => 'S-2']
            ))
        );
        $openedData   = new OpenedSessionInput(
            new \DateTimeImmutable('2024-03-01'),
            new \DateTimeImmutable('2024-09-30'),
            90
        );
        $sessionInput = new SessionInput(new ExternalId('Test', 'S-2'), 'Formation Ouverte', $openedData);

        // Act
        $result = $session->postSession($sessionInput);

        // Assert
        $this->assertInstanceOf(CreatedOutput::class, $result);
        $this->assertSame('ses_new_002', $result->agoraId->getId());
    }

    public function testPostSessionThrowsOnInvalidSessionData(): void
    {
        // Arrange
        $session      = $this->makeSession($this->makeHttpClientWithResponse(200, '{}'));
        $mockSpecific = $this->createMock(\AgoraLearningPhp\DTO\Input\Session\SpecificSessionInputInterface::class);
        $sessionInput = new SessionInput(new ExternalId('Test', 'S-3'), 'Formation Invalide', $mockSpecific);

        // Assert
        $this->expectException(\ErrorException::class);

        // Act
        $session->postSession($sessionInput);
    }

    public function testPostFixedSessionSendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $session      = $this->makeSession(
            $this->makeHttpClientInspectingBody(
                200,
                json_encode(['agoraId' => 'ses_new_004']),
                $capturedBody
            )
        );
        $fixedData    = new FixedSessionInput(
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-12-31'),
            SessionType::SESSION_TYPE_INTER,
            SessionMode::SESSION_MODE_E_LEARNING
        );
        $sessionInput = new SessionInput(
            new ExternalId('Test', 'S-4'),
            'Formation PHP',
            $fixedData,
            false,
            true,
            true,
            500.0,
            3600,
            'Description de la formation',
            20,
            'trainer-emp-001',
            'person-resp-001',
            'http://pre.example.com',
            'http://spot.example.com',
            'http://delayed.example.com'
        );

        // Act
        $session->postSession($sessionInput);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame('Formation PHP', $body['title']);
        $this->assertSame('Description de la formation', $body['description']);
        $this->assertEquals(500.0, $body['price']);
        $this->assertSame(20, $body['maxPlaces']);
        $this->assertSame(3600, $body['duration']);
        $this->assertFalse($body['manualDuration']);
        $this->assertTrue($body['hasForum']);
        $this->assertTrue($body['notifyMailForum']);
        $this->assertSame('http://pre.example.com', $body['urlPreTrainingSurvey']);
        $this->assertSame('http://spot.example.com', $body['urlOnTheSpotSurvey']);
        $this->assertSame('http://delayed.example.com', $body['urlDelayedSurvey']);
        $this->assertArrayHasKey('availabilityStartDate', $body);
        $this->assertArrayHasKey('availabilityEndDate', $body);
        $this->assertSame(SessionType::SESSION_TYPE_INTER, $body['type']);
        $this->assertSame(SessionMode::SESSION_MODE_E_LEARNING, $body['mode']);
    }

    public function testPostOpenedSessionSendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $session      = $this->makeSession(
            $this->makeHttpClientInspectingBody(
                200,
                json_encode(['agoraId' => 'ses_new_005']),
                $capturedBody
            )
        );
        $openedData   = new OpenedSessionInput(
            new \DateTimeImmutable('2024-03-01'),
            new \DateTimeImmutable('2024-09-30'),
            90
        );
        $sessionInput = new SessionInput(new ExternalId('Test', 'S-5'), 'Formation Ouverte', $openedData);

        // Act
        $session->postSession($sessionInput);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame('Formation Ouverte', $body['title']);
        $this->assertArrayHasKey('subscribeStartDate', $body);
        $this->assertArrayHasKey('subscribeEndDate', $body);
        $this->assertSame(90, $body['accessDurationDays']);
        $this->assertSame(SessionType::SESSION_TYPE_INTER, $body['type']);
        $this->assertSame(SessionMode::SESSION_MODE_E_LEARNING, $body['mode']);
    }
}
