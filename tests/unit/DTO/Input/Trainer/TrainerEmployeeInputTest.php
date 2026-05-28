<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Input\Trainer;

use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\Enum\Gender;
use PHPUnit\Framework\TestCase;

class TrainerEmployeeInputTest extends TestCase
{
    public function testConstructorWithRequiredParamsOnly(): void
    {
        $input = new TrainerEmployeeInput('Dupont', 'Jean', 'jean@example.com');

        $this->assertSame('Dupont', $input->familyName);
        $this->assertSame('Jean', $input->givenName);
        $this->assertSame('jean@example.com', $input->email);
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
    }

    public function testConstructorWithAllParams(): void
    {
        $birthDate = new \DateTimeImmutable('1985-06-15');

        $input = new TrainerEmployeeInput(
            'Martin',
            'Sophie',
            'sophie@example.com',
            false,
            false,
            Gender::GENDER_FEMALE,
            '+33600000001',
            '+33600000002',
            '10 rue Test',
            '75002',
            'Paris',
            'FR',
            'http://img.example.com/photo.jpg',
            $birthDate,
            'Formatrice',
            'society-uuid-001',
            'cv-content',
            'Master',
            'CDI',
            'Formation professionnelle'
        );

        $this->assertSame('Martin', $input->familyName);
        $this->assertSame('Sophie', $input->givenName);
        $this->assertSame('sophie@example.com', $input->email);
        $this->assertFalse($input->visibleInformation);
        $this->assertFalse($input->notifyUser);
        $this->assertSame(Gender::GENDER_FEMALE, $input->gender);
        $this->assertSame('+33600000001', $input->telephone);
        $this->assertSame('+33600000002', $input->mobileNumber);
        $this->assertSame('10 rue Test', $input->addressStreet);
        $this->assertSame('75002', $input->addressPostcode);
        $this->assertSame('Paris', $input->addressLocality);
        $this->assertSame('FR', $input->addressCountry);
        $this->assertSame('http://img.example.com/photo.jpg', $input->image);
        $this->assertSame($birthDate, $input->birthDate);
        $this->assertSame('Formatrice', $input->jobTitle);
        $this->assertSame('society-uuid-001', $input->societyId);
        $this->assertSame('cv-content', $input->cv);
        $this->assertSame('Master', $input->degree);
        $this->assertSame('CDI', $input->contract);
        $this->assertSame('Formation professionnelle', $input->jobDescription);
    }

    public function testDefaultVisibleInformationIsTrue(): void
    {
        $input = new TrainerEmployeeInput('Test', 'User', 'test@example.com');
        $this->assertTrue($input->visibleInformation);
    }

    public function testDefaultNotifyUserIsTrue(): void
    {
        $input = new TrainerEmployeeInput('Test', 'User', 'test@example.com');
        $this->assertTrue($input->notifyUser);
    }

    public function testDefaultGenderIsGenderNa(): void
    {
        $input = new TrainerEmployeeInput('Test', 'User', 'test@example.com');
        $this->assertSame(Gender::GENDER_NA, $input->gender);
    }
}
