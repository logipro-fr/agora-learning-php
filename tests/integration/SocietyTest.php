<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyPatchInput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\AgoraId;

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
        $legalPersonId = $this->client->createPerson(new PersonInput(
            $this->newExternalId('P'),
            $legalPersonFamilyName,
            $legalPersonGivenName,
            'paul.durand@example.com',
            Gender::GENDER_MALE
        ))->agoraId->getId();

        $adminPersonFamilyName = 'Girard';
        $adminPersonGivenName = 'Marie';
        $adminPersonExternalId = $this->newExternalId('P');
        $this->client->createPerson(new PersonInput(
            $adminPersonExternalId,
            $adminPersonFamilyName,
            $adminPersonGivenName,
            'marie.girard@example.com',
            Gender::GENDER_FEMALE
        ));

        $societyInput = new SocietyInput(
            $this->newExternalId('SOC'),
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
            $legalPersonId,
            $adminPersonExternalId->getId(),
            null,
            $image
        );

        //Act
        $created = $this->client->createSociety($societyInput);
        $societyOutput = $this->client->getSociety($created->agoraId);

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
        $societyInput = new SocietyInput($this->newExternalId('SOC'), $name);

        //Act
        $created = $this->client->createSociety($societyInput);
        $societyOutput = $this->client->getSociety($created->agoraId);

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
        $societyInput = new SocietyInput($this->newExternalId('SOC'), $name);
        $uuid = $this->client->createSociety($societyInput)->agoraId->getId();

        //Act
        $societyOutput = $this->client->getSociety(new AgoraId($uuid));

        // Assert
        $this->assertEquals($uuid, $societyOutput->uuid);
        $this->assertEquals($name, $societyOutput->name);
    }

    public function testGetCollectionSociety(): void
    {
        //Arrange
        $name1 = 'Société Collection Un Test';
        $societyInput1 = new SocietyInput($this->newExternalId('SOC'), $name1);
        $uuid1 = $this->client->createSociety($societyInput1)->agoraId->getId();

        $name2 = 'Société Collection Deux Test';
        $societyInput2 = new SocietyInput($this->newExternalId('SOC'), $name2);
        $uuid2 = $this->client->createSociety($societyInput2)->agoraId->getId();

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

    public function testReplaceSociety(): void
    {
        //Arrange
        $externalId = $this->newExternalId('SOC');
        $this->client->createSociety(new SocietyInput($externalId, 'Société Avant Remplacement', '12345678901234'));

        //Act
        $updated = $this->client->replaceSociety($externalId, new SocietyInput($externalId, 'Société Remplacée'));
        $societyOutput = $this->client->getSociety($externalId);

        // Assert
        $this->assertEquals($updated->agoraId->getId(), $societyOutput->uuid);
        $this->assertEquals('Société Remplacée', $societyOutput->name);
        $this->assertEmpty($societyOutput->siret);
    }

    public function testPatchSociety(): void
    {
        //Arrange
        $rhPersonId = $this->client->createPerson(new PersonInput(
            $this->newExternalId('P'),
            'Bernard',
            'Lucie',
            'lucie.bernard@example.com',
            Gender::GENDER_FEMALE
        ))->agoraId->getId();
        $created = $this->client->createSociety(new SocietyInput(
            $this->newExternalId('SOC'),
            'Société Patch',
            '12345678901234'
        ));

        //Act
        $this->client->patchSociety(
            $created->agoraId,
            (new SocietyPatchInput())->setTelephone('0102030405')->setRhPersonId($rhPersonId)
        );
        $societyOutput = $this->client->getSociety($created->agoraId);

        // Assert
        $this->assertEquals('Société Patch', $societyOutput->name);
        $this->assertEquals('12345678901234', $societyOutput->siret);
        $this->assertEquals('0102030405', $societyOutput->telephone);
        $this->assertNotNull($societyOutput->rhPerson);
        $this->assertEquals($rhPersonId, $societyOutput->rhPerson->uuid);
    }
}
