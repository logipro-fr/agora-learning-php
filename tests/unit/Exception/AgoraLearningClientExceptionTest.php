<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Exception;

use AgoraLearningPhp\Exception\AgoraLearningClientException;
use PHPUnit\Framework\TestCase;

class AgoraLearningClientExceptionTest extends TestCase
{
    public function testExtendsException(): void
    {
        $e = new AgoraLearningClientException('test message', 42);
        $this->assertInstanceOf(\Exception::class, $e);
        $this->assertSame('test message', $e->getMessage());
        $this->assertSame(42, $e->getCode());
    }

    public function testCanBeThrown(): void
    {
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Something failed');
        throw new AgoraLearningClientException('Something failed');
    }

    public function testDefaultCode(): void
    {
        $e = new AgoraLearningClientException('msg');
        $this->assertSame(0, $e->getCode());
    }
}
