<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Session;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SpecificSessionOutputInterface;
use PHPUnit\Framework\TestCase;

class OpenedSessionOutputTest extends TestCase
{
    public function testConstructor(): void
    {
        $start = new \DateTimeImmutable('2024-03-01');
        $end   = new \DateTimeImmutable('2024-09-30');

        $output = new OpenedSessionOutput($start, $end, 60);

        $this->assertSame($start, $output->subscribeStartDate);
        $this->assertSame($end, $output->subscribeEndDate);
        $this->assertSame(60, $output->accessDurationDays);
    }

    public function testImplementsInterface(): void
    {
        $output = new OpenedSessionOutput(new \DateTimeImmutable(), new \DateTimeImmutable(), 30);
        $this->assertInstanceOf(SpecificSessionOutputInterface::class, $output);
    }

    public function testExtendsApiOutput(): void
    {
        $output = new OpenedSessionOutput(new \DateTimeImmutable(), new \DateTimeImmutable(), 30);
        $this->assertInstanceOf(ApiOutput::class, $output);
    }
}
