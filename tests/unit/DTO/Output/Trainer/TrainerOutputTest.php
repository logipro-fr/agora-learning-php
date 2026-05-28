<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Trainer;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
use AgoraLearningPhp\Enum\Gender;
use PHPUnit\Framework\TestCase;

class TrainerOutputTest extends TestCase
{
    public function testConstructorWithRequiredParamsOnly(): void
    {
        $output = new TrainerOutput(
            'trainer-uuid-001',
            'Dupont',
            'Jean',
            'jean@example.com'
        );

        $this->assertSame('trainer-uuid-001', $output->uuid);
        $this->assertSame('Dupont', $output->familyName);
        $this->assertSame('Jean', $output->givenName);
        $this->assertSame('jean@example.com', $output->email);
        $this->assertTrue($output->visibleInformation);
        $this->assertSame(Gender::GENDER_NA, $output->gender);
        $this->assertSame([], $output->educationalManagerSessions);
        $this->assertNull($output->telephone);
        $this->assertNull($output->mobileNumber);
        $this->assertNull($output->addressStreet);
        $this->assertNull($output->addressPostcode);
        $this->assertNull($output->addressLocality);
        $this->assertNull($output->addressCountry);
        $this->assertNull($output->image);
        $this->assertNull($output->birthDate);
        $this->assertNull($output->jobTitle);
        $this->assertNull($output->society);
        $this->assertNull($output->cv);
        $this->assertNull($output->degree);
        $this->assertNull($output->contract);
        $this->assertNull($output->jobDescription);
    }

    public function testConstructorWithAllParams(): void
    {
        $birthDate = new \DateTimeImmutable('1980-09-01');
        $society = new SocietyOutput('soc-uuid', 'Société Test', 'test@soc.com');

        $output = new TrainerOutput(
            'trainer-uuid-002',
            'Martin',
            'Marie',
            'marie@example.com',
            false,
            Gender::GENDER_FEMALE,
            ['session-1', 'session-2'],
            '+33600000001',
            '+33600000002',
            '1 rue des Tests',
            '75000',
            'Paris',
            'FR',
            'http://img.example.com/marie.jpg',
            $birthDate,
            'Formatrice senior',
            $society,
            'cv-marie',
            'Master 2',
            'CDI',
            'Expert en formation'
        );

        $this->assertSame('trainer-uuid-002', $output->uuid);
        $this->assertSame('Martin', $output->familyName);
        $this->assertSame('Marie', $output->givenName);
        $this->assertSame('marie@example.com', $output->email);
        $this->assertFalse($output->visibleInformation);
        $this->assertSame(Gender::GENDER_FEMALE, $output->gender);
        $this->assertSame(['session-1', 'session-2'], $output->educationalManagerSessions);
        $this->assertSame('+33600000001', $output->telephone);
        $this->assertSame('+33600000002', $output->mobileNumber);
        $this->assertSame('1 rue des Tests', $output->addressStreet);
        $this->assertSame('75000', $output->addressPostcode);
        $this->assertSame('Paris', $output->addressLocality);
        $this->assertSame('FR', $output->addressCountry);
        $this->assertSame('http://img.example.com/marie.jpg', $output->image);
        $this->assertSame($birthDate, $output->birthDate);
        $this->assertSame('Formatrice senior', $output->jobTitle);
        $this->assertSame($society, $output->society);
        $this->assertSame('cv-marie', $output->cv);
        $this->assertSame('Master 2', $output->degree);
        $this->assertSame('CDI', $output->contract);
        $this->assertSame('Expert en formation', $output->jobDescription);
    }

    public function testExtendsApiOutput(): void
    {
        $output = new TrainerOutput('uuid', 'F', 'G', 'e@example.com');
        $this->assertInstanceOf(ApiOutput::class, $output);
    }

    public function testDefaultGenderIsGenderNa(): void
    {
        $output = new TrainerOutput('uuid', 'F', 'G', 'e@example.com');
        $this->assertSame(Gender::GENDER_NA, $output->gender);
    }

    public function testDefaultEducationalManagerSessionsIsEmptyArray(): void
    {
        $output = new TrainerOutput('uuid', 'F', 'G', 'e@example.com');
        $this->assertSame([], $output->educationalManagerSessions);
    }
}
