<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Person;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\Enum\Gender;
use PHPUnit\Framework\TestCase;

class PersonOutputTest extends TestCase
{
    public function testConstructorWithRequiredParamsOnly(): void
    {
        $output = new PersonOutput('uuid-001', 'Durand', 'Alice');

        $this->assertSame('uuid-001', $output->uuid);
        $this->assertSame('Durand', $output->familyName);
        $this->assertSame('Alice', $output->givenName);
        $this->assertNull($output->email);
        $this->assertNull($output->telephone);
        $this->assertNull($output->addressStreet);
        $this->assertNull($output->addressPostcode);
        $this->assertNull($output->addressLocality);
        $this->assertNull($output->addressCountry);
        $this->assertNull($output->image);
        $this->assertSame(Gender::GENDER_NA, $output->gender);
        $this->assertNull($output->birthDate);
        $this->assertNull($output->jobTitle);
    }

    public function testConstructorWithAllParams(): void
    {
        $birthDate = new \DateTimeImmutable('1988-11-05');

        $output = new PersonOutput(
            'uuid-002',
            'Bernard',
            'Luc',
            'luc@example.com',
            '+33600112233',
            '7 avenue Kléber',
            '75016',
            'Paris',
            'FR',
            'http://photo.example.com/luc.png',
            Gender::GENDER_MALE,
            $birthDate,
            'Directeur'
        );

        $this->assertSame('uuid-002', $output->uuid);
        $this->assertSame('Bernard', $output->familyName);
        $this->assertSame('Luc', $output->givenName);
        $this->assertSame('luc@example.com', $output->email);
        $this->assertSame('+33600112233', $output->telephone);
        $this->assertSame('7 avenue Kléber', $output->addressStreet);
        $this->assertSame('75016', $output->addressPostcode);
        $this->assertSame('Paris', $output->addressLocality);
        $this->assertSame('FR', $output->addressCountry);
        $this->assertSame('http://photo.example.com/luc.png', $output->image);
        $this->assertSame(Gender::GENDER_MALE, $output->gender);
        $this->assertSame($birthDate, $output->birthDate);
        $this->assertSame('Directeur', $output->jobTitle);
    }

    public function testExtendsApiOutput(): void
    {
        $output = new PersonOutput('id', 'Nom', 'Prénom');
        $this->assertInstanceOf(ApiOutput::class, $output);
    }
}
