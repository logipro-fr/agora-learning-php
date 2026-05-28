<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Input\Society;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use PHPUnit\Framework\TestCase;

class SocietyInputTest extends TestCase
{
    public function testConstructorWithNameOnly(): void
    {
        $input = new SocietyInput('Acme Corp');

        $this->assertSame('Acme Corp', $input->name);
        $this->assertNull($input->siret);
        $this->assertNull($input->legalStatus);
        $this->assertNull($input->email);
        $this->assertNull($input->telephone);
        $this->assertNull($input->addressStreetHeadOffice);
        $this->assertNull($input->addressPostcodeHeadOffice);
        $this->assertNull($input->addressLocalityHeadOffice);
        $this->assertNull($input->addressCountryHeadOffice);
        $this->assertNull($input->addressStreetInvoicing);
        $this->assertNull($input->addressPostcodeInvoicing);
        $this->assertNull($input->addressLocalityInvoicing);
        $this->assertNull($input->addressCountryInvoicing);
        $this->assertNull($input->legalPerson);
        $this->assertNull($input->administrativePerson);
        $this->assertNull($input->rhPerson);
        $this->assertNull($input->image);
    }

    public function testConstructorWithAllParams(): void
    {
        $legalPerson = new PersonInput('Legal', 'Rep');
        $adminPerson = new PersonInput('Admin', 'Rep');
        $rhPerson    = new PersonInput('RH', 'Rep');

        $input = new SocietyInput(
            'TechCorp',
            '12345678901234',
            'SAS',
            'contact@techcorp.com',
            '+33123456789',
            '10 rue de la Tech',
            '75008',
            'Paris',
            'FR',
            '10 rue Facturation',
            '75009',
            'Paris',
            'FR',
            $legalPerson,
            $adminPerson,
            $rhPerson,
            'http://example.com/logo.png'
        );

        $this->assertSame('TechCorp', $input->name);
        $this->assertSame('12345678901234', $input->siret);
        $this->assertSame('SAS', $input->legalStatus);
        $this->assertSame('contact@techcorp.com', $input->email);
        $this->assertSame('+33123456789', $input->telephone);
        $this->assertSame('10 rue de la Tech', $input->addressStreetHeadOffice);
        $this->assertSame('75008', $input->addressPostcodeHeadOffice);
        $this->assertSame('Paris', $input->addressLocalityHeadOffice);
        $this->assertSame('FR', $input->addressCountryHeadOffice);
        $this->assertSame('10 rue Facturation', $input->addressStreetInvoicing);
        $this->assertSame('75009', $input->addressPostcodeInvoicing);
        $this->assertSame('Paris', $input->addressLocalityInvoicing);
        $this->assertSame('FR', $input->addressCountryInvoicing);
        $this->assertSame($legalPerson, $input->legalPerson);
        $this->assertSame($adminPerson, $input->administrativePerson);
        $this->assertSame($rhPerson, $input->rhPerson);
        $this->assertSame('http://example.com/logo.png', $input->image);
    }
}
