<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Enum;

use AgoraLearningPhp\Enum\TrainerStatut;
use PHPUnit\Framework\TestCase;

class TrainerStatutTest extends TestCase
{
    public function provideStatutConstants(): array
    {
        return [
            'Employé' => ['STATUT_EMPLOYE', TrainerStatut::STATUT_EMPLOYE],
            'Free'    => ['STATUT_FREE',    TrainerStatut::STATUT_FREE],
        ];
    }

    /** @dataProvider provideStatutConstants */
    public function testStatutConstantHasExpectedValue(string $expected, string $actual): void
    {
        $this->assertSame($expected, $actual);
    }

    public function testConstantsAreDistinct(): void
    {
        $this->assertNotSame(TrainerStatut::STATUT_EMPLOYE, TrainerStatut::STATUT_FREE);
    }
}
