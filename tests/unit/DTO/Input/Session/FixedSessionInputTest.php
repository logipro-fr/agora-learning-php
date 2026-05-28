<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Input\Session;

use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SpecificSessionInputInterface;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use PHPUnit\Framework\TestCase;

class FixedSessionInputTest extends TestCase
{
    public function testConstructorStoresAllParams(): void
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end   = new \DateTimeImmutable('2024-12-31');

        $input = new FixedSessionInput($start, $end, SessionType::SESSION_TYPE_INTER, SessionMode::SESSION_MODE_FACE_TO_FACE);

        $this->assertSame($start, $input->availabilityStartDate);
        $this->assertSame($end, $input->availabilityEndDate);
        $this->assertSame(SessionType::SESSION_TYPE_INTER, $input->type);
        $this->assertSame(SessionMode::SESSION_MODE_FACE_TO_FACE, $input->mode);
    }

    public function testImplementsInterface(): void
    {
        $input = new FixedSessionInput(
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
            SessionType::SESSION_TYPE_INTRA,
            SessionMode::SESSION_MODE_E_LEARNING
        );

        $this->assertInstanceOf(SpecificSessionInputInterface::class, $input);
    }
}
