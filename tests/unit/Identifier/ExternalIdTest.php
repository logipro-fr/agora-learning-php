<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Identifier;

use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use AgoraLearningPhp\Identifier\ExternalId;
use PHPUnit\Framework\TestCase;

class ExternalIdTest extends TestCase
{
    public function testNominal(): void
    {
        // Act
        $externalId = new ExternalId('Src', 'S-42');

        // Assert
        $this->assertInstanceOf(ApiObjectIdentifier::class, $externalId);
        $this->assertSame('Src', $externalId->getSourceName());
        $this->assertSame('S-42', $externalId->getSourceId());
        $this->assertSame('Src:S-42', $externalId->getId());
    }

    public function testSourceNameAndIdAreTrimmed(): void
    {
        // Act
        $externalId = new ExternalId('  Src ', "\tS-42  ");

        // Assert
        $this->assertSame('Src:S-42', $externalId->getId());
    }

    public function testEmptySourceNameIsRejected(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        new ExternalId('  ', 'S-42');
    }

    public function testEmptyIdIsRejected(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        new ExternalId('Src', '  ');
    }

    public function testColonInSourceNameIsRejected(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        new ExternalId('Src:Other', 'S-42');
    }

    /**
     * @dataProvider invalidSourceNameProvider
     */
    public function testInvalidCharactersInSourceNameAreRejected(string $sourceName): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        new ExternalId($sourceName, 'S-42');
    }

    /**
     * @return array<string, array{string}>
     */
    public function invalidSourceNameProvider(): array
    {
        return [
            'espace interne' => ['My Src'],
            'slash'          => ['Src/1'],
            'point'          => ['Src.1'],
            'accent'         => ['Srcé'],
        ];
    }

    public function testColonInIdIsAcceptedAndKept(): void
    {
        // Act
        $externalId = new ExternalId('Src', 'a:b:c');

        // Assert
        $this->assertSame('a:b:c', $externalId->getSourceId());
        $this->assertSame('Src:a:b:c', $externalId->getId());
    }

    public function testSourceNameAtMaxLengthIsAccepted(): void
    {
        // Act
        $externalId = new ExternalId(str_repeat('a', 50), 'S-42');

        // Assert
        $this->assertSame(str_repeat('a', 50), $externalId->getSourceName());
    }

    public function testTooLongSourceNameIsRejected(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);

        // Act
        new ExternalId(str_repeat('a', 51), 'S-42');
    }
}
