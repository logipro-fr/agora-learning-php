<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Enum;

use AgoraLearningPhp\Enum\Gender;
use PHPUnit\Framework\TestCase;

class GenderTest extends TestCase
{
    public function provideGenderConstants(): array
    {
        return [
            'NA'     => ['GENDER_NA',     Gender::GENDER_NA],
            'Female' => ['GENDER_FEMALE', Gender::GENDER_FEMALE],
            'Male'   => ['GENDER_MALE',   Gender::GENDER_MALE],
        ];
    }

    /** @dataProvider provideGenderConstants */
    public function testGenderConstantHasExpectedValue(string $expected, string $actual): void
    {
        $this->assertSame($expected, $actual);
    }

    public function testConstantsAreDistinct(): void
    {
        $this->assertNotSame(Gender::GENDER_NA, Gender::GENDER_FEMALE);
        $this->assertNotSame(Gender::GENDER_NA, Gender::GENDER_MALE);
        $this->assertNotSame(Gender::GENDER_FEMALE, Gender::GENDER_MALE);
    }
}
