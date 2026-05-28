<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Enum;

use AgoraLearningPhp\Enum\SessionMode;
use PHPUnit\Framework\TestCase;

class SessionModeTest extends TestCase
{
    public function provideSessionModeConstants(): array
    {
        return [
            'FaceToFace' => ['FACE_TO_FACE', SessionMode::SESSION_MODE_FACE_TO_FACE],
            'ELearning'  => ['E_LEARNING',   SessionMode::SESSION_MODE_E_LEARNING],
            'Blended'    => ['BLENDED',       SessionMode::SESSION_MODE_BLENDED],
            'Distance'   => ['DISTANCE',      SessionMode::SESSION_MODE_DISTANCE],
        ];
    }

    /** @dataProvider provideSessionModeConstants */
    public function testSessionModeConstantHasExpectedValue(string $expected, string $actual): void
    {
        $this->assertSame($expected, $actual);
    }

    public function testConstantsAreDistinct(): void
    {
        $modes = [
            SessionMode::SESSION_MODE_FACE_TO_FACE,
            SessionMode::SESSION_MODE_E_LEARNING,
            SessionMode::SESSION_MODE_BLENDED,
            SessionMode::SESSION_MODE_DISTANCE,
        ];
        $this->assertSame(count($modes), count(array_unique($modes)));
    }
}
