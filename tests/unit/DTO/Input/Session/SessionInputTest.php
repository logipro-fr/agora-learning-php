<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Input\Session;

use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use PHPUnit\Framework\TestCase;

class SessionInputTest extends TestCase
{
    private function makeFixedSessionData(): FixedSessionInput
    {
        return new FixedSessionInput(
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-12-31'),
            SessionType::SESSION_TYPE_INTER,
            SessionMode::SESSION_MODE_FACE_TO_FACE
        );
    }

    public function testConstructorWithRequiredParamsOnly(): void
    {
        $sessionData = $this->makeFixedSessionData();
        $input = new SessionInput('Ma formation', $sessionData);

        $this->assertSame('Ma formation', $input->title);
        $this->assertSame($sessionData, $input->sessionData);
        $this->assertFalse($input->manualDuration);
        $this->assertTrue($input->hasForum);
        $this->assertTrue($input->notifyMailForum);
        $this->assertSame(0.0, $input->price);
        $this->assertSame(0, $input->durationInSeconds);
        $this->assertNull($input->description);
        $this->assertNull($input->maxPlaces);
        $this->assertNull($input->urlPreTrainingSurvey);
        $this->assertNull($input->urlOnTheSpotSurvey);
        $this->assertNull($input->urlDelayedSurvey);
        $this->assertNull($input->image);
    }

    public function testConstructorWithAllParams(): void
    {
        $sessionData = $this->makeFixedSessionData();

        $input = new SessionInput(
            'Formation complète',
            $sessionData,
            true,
            false,
            false,
            299.99,
            7200,
            'Une description',
            20,
            'http://pre-survey.example.com',
            'http://spot-survey.example.com',
            'http://delayed-survey.example.com',
            'http://example.com/img.jpg'
        );

        $this->assertSame('Formation complète', $input->title);
        $this->assertSame($sessionData, $input->sessionData);
        $this->assertTrue($input->manualDuration);
        $this->assertFalse($input->hasForum);
        $this->assertFalse($input->notifyMailForum);
        $this->assertSame(299.99, $input->price);
        $this->assertSame(7200, $input->durationInSeconds);
        $this->assertSame('Une description', $input->description);
        $this->assertSame(20, $input->maxPlaces);
        $this->assertSame('http://pre-survey.example.com', $input->urlPreTrainingSurvey);
        $this->assertSame('http://spot-survey.example.com', $input->urlOnTheSpotSurvey);
        $this->assertSame('http://delayed-survey.example.com', $input->urlDelayedSurvey);
        $this->assertSame('http://example.com/img.jpg', $input->image);
    }
}
