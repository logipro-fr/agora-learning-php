<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Input\Learner;

use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\Enum\Gender;
use PHPUnit\Framework\TestCase;

class LearnerInputTest extends TestCase
{
    public function testConstructorWithRequiredParamsOnly(): void
    {
        $input = new LearnerInput('Dupont', 'Jean', 'jean@example.com');

        $this->assertSame('Dupont', $input->familyName);
        $this->assertSame('Jean', $input->givenName);
        $this->assertSame('jean@example.com', $input->recoverEmail);
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
        $birthDate = new \DateTimeImmutable('1990-01-15');

        $input = new LearnerInput(
            'Martin',
            'Marie',
            'marie@example.com',
            Gender::GENDER_FEMALE,
            'marie.work@example.com',
            '+33612345678',
            '12 rue de la Paix',
            '75001',
            'Paris',
            'FR',
            'http://example.com/photo.jpg',
            $birthDate,
            'Développeur'
        );

        $this->assertSame('Martin', $input->familyName);
        $this->assertSame('Marie', $input->givenName);
        $this->assertSame('marie@example.com', $input->recoverEmail);
        $this->assertSame(Gender::GENDER_FEMALE, $input->gender);
        $this->assertSame('marie.work@example.com', $input->email);
        $this->assertSame('+33612345678', $input->telephone);
        $this->assertSame('12 rue de la Paix', $input->addressStreet);
        $this->assertSame('75001', $input->addressPostcode);
        $this->assertSame('Paris', $input->addressLocality);
        $this->assertSame('FR', $input->addressCountry);
        $this->assertSame('http://example.com/photo.jpg', $input->image);
        $this->assertSame($birthDate, $input->birthDate);
        $this->assertSame('Développeur', $input->jobTitle);
    }

    public function testDefaultGenderIsNaWhenNotProvided(): void
    {
        $input = new LearnerInput('Doe', 'John', 'john@example.com');
        $this->assertSame(Gender::GENDER_NA, $input->gender);
    }
}
