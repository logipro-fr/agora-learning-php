<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Input\Session;

use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SpecificSessionInputInterface;
use PHPUnit\Framework\TestCase;

class OpenedSessionInputTest extends TestCase
{
    public function testConstructor(): void
    {
        $start = new \DateTimeImmutable('2024-01-01');
        $end   = new \DateTimeImmutable('2024-06-30');

        $input = new OpenedSessionInput($start, $end, 90);

        $this->assertSame($start, $input->subscribeStartDate);
        $this->assertSame($end, $input->subscribeEndDate);
        $this->assertSame(90, $input->accessDurationDays);
    }

    public function testImplementsInterface(): void
    {
        $input = new OpenedSessionInput(new \DateTimeImmutable(), new \DateTimeImmutable(), 30);
        $this->assertInstanceOf(SpecificSessionInputInterface::class, $input);
    }

    public function testZeroAccessDuration(): void
    {
        $input = new OpenedSessionInput(new \DateTimeImmutable(), new \DateTimeImmutable(), 0);
        $this->assertSame(0, $input->accessDurationDays);
    }
}
