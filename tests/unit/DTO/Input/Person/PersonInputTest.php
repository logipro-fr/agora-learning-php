<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Input\Person;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\Enum\Gender;
use PHPUnit\Framework\TestCase;

class PersonInputTest extends TestCase
{
    public function testConstructorWithRequiredParamsOnly(): void
    {
        $input = new PersonInput('Durand', 'Pierre');

        $this->assertSame('Durand', $input->familyName);
        $this->assertSame('Pierre', $input->givenName);
        $this->assertSame(Gender::GENDER_NA, $input->gender);
        $this->assertNull($input->email);
        $this->assertNull($input->telephone);
        $this->assertNull($input->addressStreet);
        $this->assertNull($input->addressPostcode);
        $this->assertNull($input->addressLocality);
        $this->assertNull($input->addressCountry);
        $this->assertNull($input->image);
        $this->assertNull($input->birthDate);
        $this->assertNull($input->jobTitle);
    }

    public function testConstructorWithAllParams(): void
    {
        $birthDate = new \DateTimeImmutable('1985-06-20');

        $input = new PersonInput(
            'Leroy',
            'Sophie',
            Gender::GENDER_FEMALE,
            'sophie@example.com',
            '+33699887766',
            '5 avenue des Fleurs',
            '69000',
            'Lyon',
            'FR',
            'http://example.com/img.png',
            $birthDate,
            'Manager'
        );

        $this->assertSame('Leroy', $input->familyName);
        $this->assertSame('Sophie', $input->givenName);
        $this->assertSame(Gender::GENDER_FEMALE, $input->gender);
        $this->assertSame('sophie@example.com', $input->email);
        $this->assertSame('+33699887766', $input->telephone);
        $this->assertSame('5 avenue des Fleurs', $input->addressStreet);
        $this->assertSame('69000', $input->addressPostcode);
        $this->assertSame('Lyon', $input->addressLocality);
        $this->assertSame('FR', $input->addressCountry);
        $this->assertSame('http://example.com/img.png', $input->image);
        $this->assertSame($birthDate, $input->birthDate);
        $this->assertSame('Manager', $input->jobTitle);
    }
}
