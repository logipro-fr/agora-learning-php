<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Session;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SpecificSessionOutputInterface;
use PHPUnit\Framework\TestCase;

class FixedSessionOutputTest extends TestCase
{
    public function testConstructor(): void
    {
        $start = new \DateTimeImmutable('2024-02-01');
        $end   = new \DateTimeImmutable('2024-11-30');

        $output = new FixedSessionOutput($start, $end);

        $this->assertSame($start, $output->availabilityStartDate);
        $this->assertSame($end, $output->availabilityEndDate);
    }

    public function testImplementsInterface(): void
    {
        $output = new FixedSessionOutput(new \DateTimeImmutable(), new \DateTimeImmutable());
        $this->assertInstanceOf(SpecificSessionOutputInterface::class, $output);
    }

    public function testExtendsApiOutput(): void
    {
        $output = new FixedSessionOutput(new \DateTimeImmutable(), new \DateTimeImmutable());
        $this->assertInstanceOf(ApiOutput::class, $output);
    }
}
