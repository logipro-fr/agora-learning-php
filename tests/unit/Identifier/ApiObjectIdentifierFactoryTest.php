<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Identifier;

use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ApiObjectIdentifierFactory;
use AgoraLearningPhp\Identifier\ExternalId;
use PHPUnit\Framework\TestCase;

class ApiObjectIdentifierFactoryTest extends TestCase
{
    public function testRoundTripKeepsColonsInExternalId(): void
    {
        // Arrange
        $original = new ExternalId('Src', 'a:b:c');

        // Act
        $identifier = ApiObjectIdentifierFactory::fromString($original->getId());

        // Assert
        $this->assertInstanceOf(ExternalId::class, $identifier);
        /** @var ExternalId $identifier */
        $this->assertSame('Src', $identifier->getSourceName());
        $this->assertSame('a:b:c', $identifier->getSourceId());
    }

    public function testStringWithoutColonGivesAgoraId(): void
    {
        // Act
        $identifier = ApiObjectIdentifierFactory::fromString('ses_abc');

        // Assert
        $this->assertInstanceOf(AgoraId::class, $identifier);
        $this->assertSame('ses_abc', $identifier->getId());
    }

    public function testEmptyIdIsRejected(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        ApiObjectIdentifierFactory::fromString('Src:');
    }

    public function testEmptySourceNameIsRejected(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        ApiObjectIdentifierFactory::fromString(':id');
    }
}
