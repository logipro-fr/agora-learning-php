<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Enum;

use AgoraLearningPhp\Enum\SessionType;
use PHPUnit\Framework\TestCase;

class SessionTypeTest extends TestCase
{
    public function provideSessionTypeConstants(): array
    {
        return [
            'Inter' => ['INTER', SessionType::SESSION_TYPE_INTER],
            'Intra' => ['INTRA', SessionType::SESSION_TYPE_INTRA],
        ];
    }

    /** @dataProvider provideSessionTypeConstants */
    public function testSessionTypeConstantHasExpectedValue(string $expected, string $actual): void
    {
        $this->assertSame($expected, $actual);
    }

    public function testConstantsAreDistinct(): void
    {
        $this->assertNotSame(SessionType::SESSION_TYPE_INTER, SessionType::SESSION_TYPE_INTRA);
    }
}
