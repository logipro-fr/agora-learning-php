<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Exception;

use AgoraLearningPhp\Exception\AgoraLearningClientException;
use PHPUnit\Framework\TestCase;

class AgoraLearningClientExceptionTest extends TestCase
{
    public function testExtendsException(): void
    {
        // Act
        $e = new AgoraLearningClientException('test message', 42);

        // Assert
        $this->assertInstanceOf(\Exception::class, $e);
        $this->assertSame('test message', $e->getMessage());
        $this->assertSame(42, $e->getCode());
    }

    public function testCanBeThrown(): void
    {
        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Something failed');

        // Act
        throw new AgoraLearningClientException('Something failed');
    }

    public function testDefaultCode(): void
    {
        // Act
        $e = new AgoraLearningClientException('msg');

        // Assert
        $this->assertSame(0, $e->getCode());
    }
}
