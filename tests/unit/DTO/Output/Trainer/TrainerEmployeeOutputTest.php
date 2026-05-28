<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Trainer;

use AgoraLearningPhp\DTO\Output\Trainer\TrainerEmployeeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
use PHPUnit\Framework\TestCase;

class TrainerEmployeeOutputTest extends TestCase
{
    public function testExtendsTrainerOutput(): void
    {
        $output = new TrainerEmployeeOutput('emp-uuid-001', 'Dupont', 'Jean', 'jean@example.com');
        $this->assertInstanceOf(TrainerOutput::class, $output);
    }

    public function testCanBeInstantiatedWithRequiredParams(): void
    {
        $output = new TrainerEmployeeOutput('emp-uuid-002', 'Test', 'User', 'user@example.com');

        $this->assertSame('emp-uuid-002', $output->uuid);
        $this->assertSame('Test', $output->familyName);
        $this->assertSame('User', $output->givenName);
        $this->assertSame('user@example.com', $output->email);
    }

    public function testInheritsAllTrainerOutputProperties(): void
    {
        $birthDate = new \DateTimeImmutable('1990-05-10');

        $output = new TrainerEmployeeOutput(
            'emp-uuid-003',
            'Leroy',
            'Alice',
            'alice@example.com',
            true,
            'GENDER_NA',
            [],
            '+33611223344',
            null,
            '5 place de la République',
            '31000',
            'Toulouse',
            'FR',
            null,
            $birthDate,
            'Ingénieure',
            null,
            'cv-alice',
            'Bac+5',
            'CDD',
            'Développement logiciel'
        );

        $this->assertSame('emp-uuid-003', $output->uuid);
        $this->assertSame('Leroy', $output->familyName);
        $this->assertSame('Alice', $output->givenName);
        $this->assertSame('alice@example.com', $output->email);
        $this->assertSame('+33611223344', $output->telephone);
        $this->assertSame('5 place de la République', $output->addressStreet);
        $this->assertSame($birthDate, $output->birthDate);
        $this->assertSame('cv-alice', $output->cv);
    }
}
