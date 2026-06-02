<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\Enum\Gender;

class SocietyTest extends AbstractAgoraLearningTest
{
    public function testCreateCompleteSociety(): void
    {
        //Arrange
        $name = 'Formation Excellence SAS';
        $siret = '12345678901234';
        $legalStatus = 'SAS';
        $email = 'contact@formation-excellence.fr';
        $telephone = '0145678901';
        $addressStreetHeadOffice = '15 rue de la Formation';
        $addressPostcodeHeadOffice = '75008';
        $addressLocalityHeadOffice = 'Paris';
        $addressCountryHeadOffice = 'France';
        $addressStreetInvoicing = '15 rue de la Facturation';
        $addressPostcodeInvoicing = '75009';
        $addressLocalityInvoicing = 'Paris';
        $addressCountryInvoicing = 'France';
        $image = 'https://www.agora-learning.com/apiAgoraLearning/ressources/logo.png';

        $legalPersonFamilyName = 'Durand';
        $legalPersonGivenName = 'Paul';
        $legalPerson = new PersonInput(
            $legalPersonFamilyName,
            $legalPersonGivenName,
            'paul.durand@example.com',
            Gender::GENDER_MALE
        );

        $adminPersonFamilyName = 'Girard';
        $adminPersonGivenName = 'Marie';
        $adminPerson = new PersonInput(
            $adminPersonFamilyName,
            $adminPersonGivenName,
            'marie.girard@example.com',
            Gender::GENDER_FEMALE
        );

        $societyInput = new SocietyInput(
            $name,
            $siret,
            $legalStatus,
            $email,
            $telephone,
            $addressStreetHeadOffice,
            $addressPostcodeHeadOffice,
            $addressLocalityHeadOffice,
            $addressCountryHeadOffice,
            $addressStreetInvoicing,
            $addressPostcodeInvoicing,
            $addressLocalityInvoicing,
            $addressCountryInvoicing,
            $legalPerson,
            $adminPerson,
            null,
            $image
        );

        //Act
        $societyOutput = $this->client->createSociety($societyInput);

        // Assert
        $this->assertEquals($name, $societyOutput->name);
        $this->assertEquals($siret, $societyOutput->siret);
        $this->assertEquals($legalStatus, $societyOutput->legalStatus);
        $this->assertEquals($email, $societyOutput->email);
        $this->assertEquals($telephone, $societyOutput->telephone);
        $this->assertEquals($addressStreetHeadOffice, $societyOutput->addressStreetHeadOffice);
        $this->assertEquals($addressPostcodeHeadOffice, $societyOutput->addressPostcodeHeadOffice);
        $this->assertEquals($addressLocalityHeadOffice, $societyOutput->addressLocalityHeadOffice);
        $this->assertEquals($addressCountryHeadOffice, $societyOutput->addressCountryHeadOffice);
        $this->assertEquals($addressStreetInvoicing, $societyOutput->addressStreetInvoicing);
        $this->assertEquals($addressPostcodeInvoicing, $societyOutput->addressPostcodeInvoicing);
        $this->assertEquals($addressLocalityInvoicing, $societyOutput->addressLocalityInvoicing);
        $this->assertEquals($addressCountryInvoicing, $societyOutput->addressCountryInvoicing);
        $this->assertNotNull($societyOutput->legalPerson);
        $this->assertEquals($legalPersonFamilyName, $societyOutput->legalPerson->familyName);
        $this->assertEquals($legalPersonGivenName, $societyOutput->legalPerson->givenName);
        $this->assertNotNull($societyOutput->administrativePerson);
        $this->assertEquals($adminPersonFamilyName, $societyOutput->administrativePerson->familyName);
        $this->assertEquals($adminPersonGivenName, $societyOutput->administrativePerson->givenName);
        $this->assertNull($societyOutput->rhPerson);
    }

    public function testCreateMinimumSociety(): void
    {
        //Arrange
        $name = 'Société Minimum Test';
        $societyInput = new SocietyInput($name);

        //Act
        $societyOutput = $this->client->createSociety($societyInput);

        // Assert
        $this->assertEquals($name, $societyOutput->name);
        $this->assertEmpty($societyOutput->siret);
        $this->assertEmpty($societyOutput->legalStatus);
        $this->assertEmpty($societyOutput->email);
        $this->assertEmpty($societyOutput->telephone);
        $this->assertEmpty($societyOutput->addressStreetHeadOffice);
        $this->assertEmpty($societyOutput->addressPostcodeHeadOffice);
        $this->assertEmpty($societyOutput->addressLocalityHeadOffice);
        $this->assertEmpty($societyOutput->addressCountryHeadOffice);
        $this->assertEmpty($societyOutput->addressStreetInvoicing);
        $this->assertEmpty($societyOutput->addressPostcodeInvoicing);
        $this->assertEmpty($societyOutput->addressLocalityInvoicing);
        $this->assertEmpty($societyOutput->addressCountryInvoicing);
        $this->assertNull($societyOutput->legalPerson);
        $this->assertNull($societyOutput->administrativePerson);
        $this->assertNull($societyOutput->rhPerson);
    }

    public function testGetSociety(): void
    {
        //Arrange
        $name = 'Société Get Test';
        $societyInput = new SocietyInput($name);
        $output = $this->client->createSociety($societyInput);
        $uuid = $output->uuid;

        //Act
        $societyOutput = $this->client->getSociety($uuid);

        // Assert
        $this->assertEquals($uuid, $societyOutput->uuid);
        $this->assertEquals($name, $societyOutput->name);
    }

    public function testGetCollectionSociety(): void
    {
        //Arrange
        $name1 = 'Société Collection Un Test';
        $societyInput1 = new SocietyInput($name1);
        $output1 = $this->client->createSociety($societyInput1);
        $uuid1 = $output1->uuid;

        $name2 = 'Société Collection Deux Test';
        $societyInput2 = new SocietyInput($name2);
        $output2 = $this->client->createSociety($societyInput2);
        $uuid2 = $output2->uuid;

        //Act
        $societyOutputs = $this->client->getCollectionSociety();

        // Assert
        foreach ($societyOutputs as $societyOutput) {
            if ($uuid1 === $societyOutput->uuid) {
                $this->assertEquals($uuid1, $societyOutput->uuid);
                $this->assertEquals($name1, $societyOutput->name);
            } elseif ($uuid2 === $societyOutput->uuid) {
                $this->assertEquals($uuid2, $societyOutput->uuid);
                $this->assertEquals($name2, $societyOutput->name);
            }
        }
    }
}
