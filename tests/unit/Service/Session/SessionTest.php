<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\Session;

use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Session\Session;
use AgoraLearningPhp\Tests\Service\ServiceTestCase;

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

    // -------------------------------------------------------------------------
    // convertDataToDTO — logique pure, sans réseau
    // -------------------------------------------------------------------------

    public function testConvertDataToDTOReturnsSessionOutput(): void
    {
        $dto = Session::convertDataToDTO(
            array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())
        );

        $this->assertInstanceOf(SessionOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        $dto = Session::convertDataToDTO(
            array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())
        );

        $this->assertSame('sess-uuid-001', $dto->uuid);
        $this->assertSame('Formation PHP', $dto->title);
        $this->assertSame(SessionType::SESSION_TYPE_INTER, $dto->type);
        $this->assertSame(SessionMode::SESSION_MODE_E_LEARNING, $dto->mode);
        $this->assertFalse($dto->manualDuration);
        $this->assertTrue($dto->hasForum);
    }

    public function testConvertDataToDTOReturnsFixedSessionData(): void
    {
        $dto = Session::convertDataToDTO(
            array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())
        );

        $this->assertInstanceOf(FixedSessionOutput::class, $dto->sessionData);
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->sessionData->availabilityStartDate);
        $this->assertSame('2024-01-01', $dto->sessionData->availabilityStartDate->format('Y-m-d'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->sessionData->availabilityEndDate);
        $this->assertSame('2024-12-31', $dto->sessionData->availabilityEndDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOReturnsOpenedSessionData(): void
    {
        $dto = Session::convertDataToDTO(
            array_merge($this->makeBaseSessionData(), $this->makeOpenedSessionData())
        );

        $this->assertInstanceOf(OpenedSessionOutput::class, $dto->sessionData);
        $this->assertSame(90, $dto->sessionData->accessDurationDays);
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->sessionData->subscribeStartDate);
        $this->assertSame('2024-03-01', $dto->sessionData->subscribeStartDate->format('Y-m-d'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->sessionData->subscribeEndDate);
        $this->assertSame('2024-09-30', $dto->sessionData->subscribeEndDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOMapsOptionalFields(): void
    {
        $data = array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData(), [
            'duration'             => '7200',
            'description'          => 'Une description',
            'maxPlaces'            => 30,
            'urlPreTrainingSurvey' => 'http://pre.example.com',
            'urlOnTheSpotSurvey'   => 'http://spot.example.com',
            'urlDelayedSurvey'     => 'http://delayed.example.com',
            'image'                => 'http://img.example.com/session.jpg',
        ]);

        $dto = Session::convertDataToDTO($data);

        $this->assertSame('7200', $dto->duration);
        $this->assertSame('Une description', $dto->description);
        $this->assertSame(30, $dto->maxPlaces);
        $this->assertSame('http://pre.example.com', $dto->urlPreTrainingSurvey);
        $this->assertSame('http://spot.example.com', $dto->urlOnTheSpotSurvey);
        $this->assertSame('http://delayed.example.com', $dto->urlDelayedSurvey);
        $this->assertSame('http://img.example.com/session.jpg', $dto->image);
    }

    public function testConvertDataToDTOThrowsExceptionWhenNoSessionType(): void
    {
        $this->expectException(\Exception::class);

        Session::convertDataToDTO($this->makeBaseSessionData());
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionSessionReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        $session = $this->makeSession($this->makeHttpClientWithResponse(200, '[]'));

        $result = $session->getCollectionSession();

        $this->assertSame([], $result);
    }

    public function testGetCollectionSessionReturnsDTOArray(): void
    {
        $row1    = array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData(), ['id' => 's1']);
        $row2    = array_merge($this->makeBaseSessionData(), $this->makeOpenedSessionData(), ['id' => 's2']);
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(200, json_encode([$row1, $row2]))
        );

        $result = $session->getCollectionSession();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(SessionOutput::class, $result[0]);
        $this->assertSame('s1', $result[0]->uuid);
    }

    public function testGetSessionReturnsSingleDTO(): void
    {
        $session = $this->makeSession(
            $this->makeHttpClientWithResponse(200, json_encode(
                array_merge($this->makeBaseSessionData(), $this->makeFixedSessionData())
            ))
        );

        $result = $session->getSession('sess-uuid-001');

        $this->assertInstanceOf(SessionOutput::class, $result);
        $this->assertSame('sess-uuid-001', $result->uuid);
    }

    public function testPostSessionWithFixedSessionInput(): void
    {
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

        $result = $session->postSession($sessionInput);

        $this->assertInstanceOf(SessionOutput::class, $result);
    }

    public function testPostSessionWithOpenedSessionInput(): void
    {
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

        $result = $session->postSession($sessionInput);

        $this->assertInstanceOf(SessionOutput::class, $result);
    }

    public function testPostSessionThrowsOnInvalidSessionData(): void
    {
        $session      = $this->makeSession($this->makeHttpClientWithResponse(200, '{}'));
        $mockSpecific = $this->createMock(\AgoraLearningPhp\DTO\Input\Session\SpecificSessionInputInterface::class);
        $sessionInput = new SessionInput('Formation Invalide', $mockSpecific);

        $this->expectException(\ErrorException::class);

        $session->postSession($sessionInput);
    }
}
