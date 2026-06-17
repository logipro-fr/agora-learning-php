<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Session;

use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Enum\TrainerStatut;
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
            'id'              => 'sess-uuid-001',
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
        return new class($httpClient) extends Session {
            public function __construct(HttpClient $client)
            {
                $this->httpClient = $client;
            }
        };
    }

    private function makePersonData(): array
    {
        return [
            'id'         => 'person-uuid-001',
            'familyName' => 'Doe',
            'givenName'  => 'John',
            'email' => 'john.doe@example.net'
        ];
    }

    private function makeTrainerData(): array
    {
        return [
            'id'                        => 'trainer-emp-001',
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
        $row1    = array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData(), ['id' => 's1']);
        $row2    = array_merge($this->makeBaseSessionData(), $this->makeOpenedSessionData(), ['id' => 's2']);
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
        $result = $session->getSession('sess-uuid-001');

        // Assert
        $this->assertInstanceOf(SessionOutput::class, $result);
        $this->assertSame('sess-uuid-001', $result->uuid);
    }

    public function testPostSessionWithFixedSessionInput(): void
    {
        // Arrange
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(200, json_encode(
                array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())
            ))
        );
        $fixedData    = new FixedSessionInput(
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-12-31'),
            SessionType::SESSION_TYPE_INTER,
            SessionMode::SESSION_MODE_FACE_TO_FACE
        );
        $sessionInput = new SessionInput('Formation PHP', $fixedData);

        // Act
        $result = $session->postSession($sessionInput);

        // Assert
        $this->assertInstanceOf(SessionOutput::class, $result);
    }

    public function testPostSessionWithOpenedSessionInput(): void
    {
        // Arrange
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(200, json_encode(
                array_merge($this->makeBaseSessionData(), $this->makeOpenedSessionData())
            ))
        );
        $openedData   = new OpenedSessionInput(
            new \DateTimeImmutable('2024-03-01'),
            new \DateTimeImmutable('2024-09-30'),
            90
        );
        $sessionInput = new SessionInput('Formation Ouverte', $openedData);

        // Act
        $result = $session->postSession($sessionInput);

        // Assert
        $this->assertInstanceOf(SessionOutput::class, $result);
    }

    public function testPostSessionThrowsOnInvalidSessionData(): void
    {
        // Arrange
        $session      = $this->makeSession($this->makeHttpClientWithResponse(200, '{}'));
        $mockSpecific = $this->createMock(\AgoraLearningPhp\DTO\Input\Session\SpecificSessionInputInterface::class);
        $sessionInput = new SessionInput('Formation Invalide', $mockSpecific);

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
                json_encode(array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())),
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
                json_encode(array_merge($this->makeBaseSessionData(), $this->makeOpenedSessionData())),
                $capturedBody
            )
        );
        $openedData   = new OpenedSessionInput(
            new \DateTimeImmutable('2024-03-01'),
            new \DateTimeImmutable('2024-09-30'),
            90
        );
        $sessionInput = new SessionInput('Formation Ouverte', $openedData);

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
