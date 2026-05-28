<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Society;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use PHPUnit\Framework\TestCase;

class SocietyOutputTest extends TestCase
{
    public function testConstructorWithRequiredParamsOnly(): void
    {
        $output = new SocietyOutput('soc-uuid-001', 'MaSociété');

        $this->assertSame('soc-uuid-001', $output->uuid);
        $this->assertSame('MaSociété', $output->name);
        $this->assertNull($output->siret);
        $this->assertNull($output->legalStatus);
        $this->assertNull($output->email);
        $this->assertNull($output->telephone);
        $this->assertNull($output->addressStreetHeadOffice);
        $this->assertNull($output->addressPostcodeHeadOffice);
        $this->assertNull($output->addressLocalityHeadOffice);
        $this->assertNull($output->addressCountryHeadOffice);
        $this->assertNull($output->addressStreetInvoicing);
        $this->assertNull($output->addressPostcodeInvoicing);
        $this->assertNull($output->addressLocalityInvoicing);
        $this->assertNull($output->addressCountryInvoicing);
        $this->assertNull($output->legalPerson);
        $this->assertNull($output->administrativePerson);
        $this->assertNull($output->rhPerson);
        $this->assertNull($output->image);
    }

    public function testConstructorWithAllParams(): void
    {
        $legalPerson = new PersonOutput('p1', 'Legal', 'Rep');
        $adminPerson = new PersonOutput('p2', 'Admin', 'Rep');
        $rhPerson    = new PersonOutput('p3', 'RH', 'Rep');

        $output = new SocietyOutput(
            'soc-uuid-002',
            'BigCorp',
            '98765432100012',
            'SARL',
            'contact@bigcorp.com',
            '+33456789012',
            '1 place de la République',
            '75003',
            'Paris',
            'FR',
            '2 rue de la Facturation',
            '75004',
            'Paris',
            'FR',
            $legalPerson,
            $adminPerson,
            $rhPerson,
            'http://bigcorp.com/logo.png'
        );

        $this->assertSame('soc-uuid-002', $output->uuid);
        $this->assertSame('BigCorp', $output->name);
        $this->assertSame('98765432100012', $output->siret);
        $this->assertSame('SARL', $output->legalStatus);
        $this->assertSame('contact@bigcorp.com', $output->email);
        $this->assertSame('+33456789012', $output->telephone);
        $this->assertSame($legalPerson, $output->legalPerson);
        $this->assertSame($adminPerson, $output->administrativePerson);
        $this->assertSame($rhPerson, $output->rhPerson);
        $this->assertSame('http://bigcorp.com/logo.png', $output->image);
    }

    public function testExtendsApiOutput(): void
    {
        $output = new SocietyOutput('id', 'Name');
        $this->assertInstanceOf(ApiOutput::class, $output);
    }
}
