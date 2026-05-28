<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Trainer;

use AgoraLearningPhp\DTO\Output\Trainer\TrainerFreeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
use AgoraLearningPhp\Enum\Gender;
use PHPUnit\Framework\TestCase;

class TrainerFreeOutputTest extends TestCase
{
    public function testExtendsTrainerOutput(): void
    {
        $output = new TrainerFreeOutput('free-uuid-001', 'Dupont', 'Jean', 'jean@example.com');
        $this->assertInstanceOf(TrainerOutput::class, $output);
    }

    public function testConstructorWithRequiredParamsOnly(): void
    {
        $output = new TrainerFreeOutput('free-uuid-002', 'Leclerc', 'Paul', 'paul@example.com');

        $this->assertSame('free-uuid-002', $output->uuid);
        $this->assertSame('Leclerc', $output->familyName);
        $this->assertSame('Paul', $output->givenName);
        $this->assertSame('paul@example.com', $output->email);
        $this->assertTrue($output->visibleInformation);
        $this->assertSame(Gender::GENDER_NA, $output->gender);
        $this->assertSame([], $output->educationalManagerSessions);
        $this->assertNull($output->telephone);
        $this->assertNull($output->hourlyCost);
        $this->assertNull($output->daylyCost);
        $this->assertNull($output->siret);
        $this->assertNull($output->tvaNumber);
        $this->assertNull($output->urssafNumber);
        $this->assertNull($output->urssafCertificate);
        $this->assertNull($output->billingAddressStreet);
        $this->assertNull($output->billingAddressPostcode);
        $this->assertNull($output->billingAddressLocality);
        $this->assertNull($output->billingAddressCountry);
    }

    public function testConstructorWithFreeSpecificParams(): void
    {
        $output = new TrainerFreeOutput(
            'free-uuid-003',
            'Durand',
            'Michel',
            'michel@example.com',
            true,
            Gender::GENDER_MALE,
            ['session-x'],
            '+33611000001',
            '+33611000002',
            '7 allée Free',
            '44000',
            'Nantes',
            'FR',
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            85.00,
            680.00,
            '98765432101234',
            'FR98765432101',
            'URSSAF456',
            'cert-url-2',
            '10 rue Billing',
            '44001',
            'Nantes Cedex',
            'FR'
        );

        $this->assertSame('free-uuid-003', $output->uuid);
        $this->assertSame(Gender::GENDER_MALE, $output->gender);
        $this->assertSame(['session-x'], $output->educationalManagerSessions);
        $this->assertSame(85.00, $output->hourlyCost);
        $this->assertSame(680.00, $output->daylyCost);
        $this->assertSame('98765432101234', $output->siret);
        $this->assertSame('FR98765432101', $output->tvaNumber);
        $this->assertSame('URSSAF456', $output->urssafNumber);
        $this->assertSame('cert-url-2', $output->urssafCertificate);
        $this->assertSame('10 rue Billing', $output->billingAddressStreet);
        $this->assertSame('44001', $output->billingAddressPostcode);
        $this->assertSame('Nantes Cedex', $output->billingAddressLocality);
        $this->assertSame('FR', $output->billingAddressCountry);
    }
}
