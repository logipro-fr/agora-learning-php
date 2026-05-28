<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Input\Trainer;

use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\Enum\Gender;
use PHPUnit\Framework\TestCase;

class TrainerFreeInputTest extends TestCase
{
    public function testConstructorWithRequiredParamsOnly(): void
    {
        $input = new TrainerFreeInput('Leclerc', 'Paul', 'paul@example.com');

        $this->assertSame('Leclerc', $input->familyName);
        $this->assertSame('Paul', $input->givenName);
        $this->assertSame('paul@example.com', $input->email);
        $this->assertTrue($input->visibleInformation);
        $this->assertTrue($input->notifyUser);
        $this->assertSame(Gender::GENDER_NA, $input->gender);
        $this->assertNull($input->telephone);
        $this->assertNull($input->mobileNumber);
        $this->assertNull($input->addressStreet);
        $this->assertNull($input->addressPostcode);
        $this->assertNull($input->addressLocality);
        $this->assertNull($input->addressCountry);
        $this->assertNull($input->image);
        $this->assertNull($input->birthDate);
        $this->assertNull($input->jobTitle);
        $this->assertNull($input->societyId);
        $this->assertNull($input->cv);
        $this->assertNull($input->degree);
        $this->assertNull($input->contract);
        $this->assertNull($input->jobDescription);
        $this->assertNull($input->hourlyCost);
        $this->assertNull($input->daylyCost);
        $this->assertNull($input->siret);
        $this->assertNull($input->tvaNumber);
        $this->assertNull($input->urssafNumber);
        $this->assertNull($input->urssafCertificate);
        $this->assertNull($input->billingAddressStreet);
        $this->assertNull($input->billingAddressPostcode);
        $this->assertNull($input->billingAddressLocality);
        $this->assertNull($input->billingAddressCountry);
    }

    public function testConstructorWithAllParams(): void
    {
        $birthDate = new \DateTimeImmutable('1978-03-22');

        $input = new TrainerFreeInput(
            'Durand',
            'Michel',
            'michel@example.com',
            false,
            false,
            Gender::GENDER_MALE,
            '+33600000010',
            '+33600000011',
            '5 avenue Free',
            '69001',
            'Lyon',
            'FR',
            'http://img.example.com/michel.jpg',
            $birthDate,
            'Consultant',
            'society-uuid-002',
            'my-cv',
            'Licence',
            'Indépendant',
            'Consulting tech',
            75.50,
            600.00,
            '12345678901234',
            'FR12345678901',
            'URSSAF123',
            'cert-url',
            '1 rue Facturation',
            '13001',
            'Marseille',
            'FR'
        );

        $this->assertSame('Durand', $input->familyName);
        $this->assertSame('Michel', $input->givenName);
        $this->assertSame('michel@example.com', $input->email);
        $this->assertFalse($input->visibleInformation);
        $this->assertFalse($input->notifyUser);
        $this->assertSame(Gender::GENDER_MALE, $input->gender);
        $this->assertSame('+33600000010', $input->telephone);
        $this->assertSame('+33600000011', $input->mobileNumber);
        $this->assertSame('5 avenue Free', $input->addressStreet);
        $this->assertSame('69001', $input->addressPostcode);
        $this->assertSame('Lyon', $input->addressLocality);
        $this->assertSame('FR', $input->addressCountry);
        $this->assertSame('http://img.example.com/michel.jpg', $input->image);
        $this->assertSame($birthDate, $input->birthDate);
        $this->assertSame('Consultant', $input->jobTitle);
        $this->assertSame('society-uuid-002', $input->societyId);
        $this->assertSame('my-cv', $input->cv);
        $this->assertSame('Licence', $input->degree);
        $this->assertSame('Indépendant', $input->contract);
        $this->assertSame('Consulting tech', $input->jobDescription);
        $this->assertSame(75.50, $input->hourlyCost);
        $this->assertSame(600.00, $input->daylyCost);
        $this->assertSame('12345678901234', $input->siret);
        $this->assertSame('FR12345678901', $input->tvaNumber);
        $this->assertSame('URSSAF123', $input->urssafNumber);
        $this->assertSame('cert-url', $input->urssafCertificate);
        $this->assertSame('1 rue Facturation', $input->billingAddressStreet);
        $this->assertSame('13001', $input->billingAddressPostcode);
        $this->assertSame('Marseille', $input->billingAddressLocality);
        $this->assertSame('FR', $input->billingAddressCountry);
    }

    public function testDefaultGenderIsGenderNa(): void
    {
        $input = new TrainerFreeInput('Test', 'User', 'test@example.com');
        $this->assertSame(Gender::GENDER_NA, $input->gender);
    }
}
