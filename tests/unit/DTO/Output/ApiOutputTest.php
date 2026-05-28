<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use PHPUnit\Framework\TestCase;

class ApiOutputTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $output = new ApiOutput();
        $this->assertInstanceOf(ApiOutput::class, $output);
    }
}
