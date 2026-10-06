<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Identifier;

use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use PHPUnit\Framework\TestCase;

class AgoraIdTest extends TestCase
{
    public function testValidId(): void
    {
        // Act
        $agoraId = new AgoraId('ses_01KRXPMF97F8BRPH7N7972QRYY');

        // Assert
        $this->assertInstanceOf(ApiObjectIdentifier::class, $agoraId);
        $this->assertSame('ses_01KRXPMF97F8BRPH7N7972QRYY', $agoraId->getId());
    }

    public function testIdIsTrimmed(): void
    {
        // Act
        $agoraId = new AgoraId("  ses_abc\t\n");

        // Assert
        $this->assertSame('ses_abc', $agoraId->getId());
    }

    public function testEmptyIdIsRejected(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        new AgoraId('');
    }

    public function testWhitespaceOnlyIdIsRejected(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        new AgoraId("   \t ");
    }

    public function testIdContainingColonIsRejected(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('ExternalId');

        // Act
        new AgoraId('Src:S-42');
    }
}
