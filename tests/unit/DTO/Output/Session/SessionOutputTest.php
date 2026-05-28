<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Session;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use PHPUnit\Framework\TestCase;

class SessionOutputTest extends TestCase
{
    private function makeFixedSessionData(): FixedSessionOutput
    {
        return new FixedSessionOutput(
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-12-31')
        );
    }

    public function testConstructorWithRequiredParamsOnly(): void
    {
        $sessionData = $this->makeFixedSessionData();

        $output = new SessionOutput(
            'sess-uuid-001',
            'Formation PHP',
            SessionType::SESSION_TYPE_INTER,
            SessionMode::SESSION_MODE_E_LEARNING,
            $sessionData
        );

        $this->assertSame('sess-uuid-001', $output->uuid);
        $this->assertSame('Formation PHP', $output->title);
        $this->assertSame(SessionType::SESSION_TYPE_INTER, $output->type);
        $this->assertSame(SessionMode::SESSION_MODE_E_LEARNING, $output->mode);
        $this->assertSame($sessionData, $output->sessionData);
        $this->assertFalse($output->manualDuration);
        $this->assertTrue($output->hasForum);
        $this->assertTrue($output->notifyMailForum);
        $this->assertSame(0.0, $output->price);
        $this->assertNull($output->duration);
        $this->assertNull($output->description);
        $this->assertNull($output->maxPlaces);
        $this->assertNull($output->urlPreTrainingSurvey);
        $this->assertNull($output->urlOnTheSpotSurvey);
        $this->assertNull($output->urlDelayedSurvey);
        $this->assertNull($output->image);
    }

    public function testConstructorWithAllParams(): void
    {
        $sessionData = $this->makeFixedSessionData();

        $output = new SessionOutput(
            'sess-uuid-002',
            'Formation Avancée',
            SessionType::SESSION_TYPE_INTRA,
            SessionMode::SESSION_MODE_BLENDED,
            $sessionData,
            true,
            false,
            false,
            499.0,
            '7200',
            'Une description complète',
            15,
            'http://pre-survey.example.com',
            'http://spot-survey.example.com',
            'http://delayed-survey.example.com',
            'http://img.example.com/session.jpg'
        );

        $this->assertSame('sess-uuid-002', $output->uuid);
        $this->assertSame('Formation Avancée', $output->title);
        $this->assertTrue($output->manualDuration);
        $this->assertFalse($output->hasForum);
        $this->assertFalse($output->notifyMailForum);
        $this->assertSame(499.0, $output->price);
        $this->assertSame('7200', $output->duration);
        $this->assertSame('Une description complète', $output->description);
        $this->assertSame(15, $output->maxPlaces);
        $this->assertSame('http://pre-survey.example.com', $output->urlPreTrainingSurvey);
        $this->assertSame('http://spot-survey.example.com', $output->urlOnTheSpotSurvey);
        $this->assertSame('http://delayed-survey.example.com', $output->urlDelayedSurvey);
        $this->assertSame('http://img.example.com/session.jpg', $output->image);
    }

    public function testExtendsApiOutput(): void
    {
        $output = new SessionOutput('id', 'Title', 'TYPE', 'MODE', $this->makeFixedSessionData());
        $this->assertInstanceOf(ApiOutput::class, $output);
    }
}
