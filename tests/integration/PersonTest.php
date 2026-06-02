<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\Enum\Gender;

class PersonTest extends AbstractAgoraLearningTest
{
    public function testCreateCompletePerson(): void
    {
        //Arrange
        $familyName = 'Grand';
        $givenName = 'Jean';
        $email = 'jean.grand@example.com';
        $gender = Gender::GENDER_MALE;
        $telephone = '0123456789';
        $addressStreet = '3 rue de la Paix';
        $addressPostcode = '75001';
        $addressLocality = 'Paris';
        $addressCountry = 'France';
        $image = '';
        $birthDate = new \DateTimeImmutable('1985-06-15');
        $jobTitle = 'Développeur';
        $personInput = new PersonInput(
            $familyName,
            $givenName,
            $email,
            $gender,
            $telephone,
            $addressStreet,
            $addressPostcode,
            $addressLocality,
            $addressCountry,
            $image,
            $birthDate,
            $jobTitle
        );

        //Act
        $personOutput = $this->client->createPerson($personInput);

        // Assert
        $this->assertEquals($familyName, $personOutput->familyName);
        $this->assertEquals($givenName, $personOutput->givenName);
        $this->assertEquals($gender, $personOutput->gender);
        $this->assertEquals($email, $personOutput->email);
        $this->assertEquals($telephone, $personOutput->telephone);
        $this->assertEquals($addressStreet, $personOutput->addressStreet);
        $this->assertEquals($addressPostcode, $personOutput->addressPostcode);
        $this->assertEquals($addressLocality, $personOutput->addressLocality);
        $this->assertEquals($addressCountry, $personOutput->addressCountry);
        $this->assertEquals($birthDate, $personOutput->birthDate);
        $this->assertEquals($jobTitle, $personOutput->jobTitle);
    }

    public function testCreateMinimumPerson(): void
    {
        //Arrange
        $familyName = 'Petit';
        $givenName = 'Jean';
        $email = 'jean.grand@example.com';
        $gender = Gender::GENDER_MALE;
        $personInput = new PersonInput(
            $familyName,
            $givenName,
            $email,
            $gender
        );

        //Act
        $personOutput = $this->client->createPerson($personInput);

        // Assert
        $this->assertEquals($familyName, $personOutput->familyName);
        $this->assertEquals($givenName, $personOutput->givenName);
        $this->assertEquals($email, $personOutput->email);
        $this->assertEquals($gender, $personOutput->gender);
        $this->assertNull($personOutput->telephone);
        $this->assertNull($personOutput->addressStreet);
        $this->assertNull($personOutput->addressPostcode);
        $this->assertNull($personOutput->addressLocality);
        $this->assertNull($personOutput->addressCountry);
        $this->assertNull($personOutput->birthDate);
        $this->assertNull($personOutput->jobTitle);
    }

    public function testGetPerson(): void
    {
        //Arrange
        $familyName = 'Pris';
        $givenName = 'Jean';
        $email = 'jean.pris@example.com';
        $gender = Gender::GENDER_MALE;
        $personInput = new PersonInput(
            $familyName,
            $givenName,
            $email,
            $gender
        );
        $output = $this->client->createPerson($personInput);
        $uuid = $output->uuid;

        //Act
        $personOutput = $this->client->getPerson($uuid);

        // Assert
        $this->assertEquals($uuid, $personOutput->uuid);
        $this->assertEquals($familyName, $personOutput->familyName);
        $this->assertEquals($givenName, $personOutput->givenName);
        $this->assertEquals($email, $personOutput->email);
        $this->assertEquals($gender, $personOutput->gender);
        $this->assertNull($personOutput->telephone);
        $this->assertNull($personOutput->addressStreet);
        $this->assertNull($personOutput->addressPostcode);
        $this->assertNull($personOutput->addressLocality);
        $this->assertNull($personOutput->addressCountry);
        $this->assertNull($personOutput->birthDate);
        $this->assertNull($personOutput->jobTitle);
    }

    public function testGetCollectionPerson(): void
    {
        //Arrange
        $familyName1 = 'Un';
        $givenName1 = 'Jean';
        $email1 = 'jean.un@example.com';
        $gender1 = Gender::GENDER_MALE;
        $personInput1 = new PersonInput(
            $familyName1,
            $givenName1,
            $email1,
            $gender1
        );
        $output1 = $this->client->createPerson($personInput1);
        $uuid1 = $output1->uuid;

        $familyName2 = 'Deux';
        $givenName2 = 'Jean';
        $email2 = 'jean.deux@example.com';
        $gender2 = Gender::GENDER_MALE;
        $personInput2 = new PersonInput(
            $familyName2,
            $givenName2,
            $email2,
            $gender2
        );
        $output2 = $this->client->createPerson($personInput2);
        $uuid2 = $output2->uuid;

        //Act
        $personOutputs = $this->client->getCollectionPerson();

        // Assert
        foreach ($personOutputs as $personOutput) {
            if ($uuid1 === $personOutput->uuid) {
                $this->assertEquals($uuid1, $personOutput->uuid);
                $this->assertEquals($familyName1, $personOutput->familyName);
                $this->assertEquals($givenName1, $personOutput->givenName);
                $this->assertEquals($email1, $personOutput->email);
                $this->assertEquals($gender1, $personOutput->gender);
                $this->assertNull($personOutput->telephone);
                $this->assertNull($personOutput->addressStreet);
                $this->assertNull($personOutput->addressPostcode);
                $this->assertNull($personOutput->addressLocality);
                $this->assertNull($personOutput->addressCountry);
                $this->assertNull($personOutput->birthDate);
                $this->assertNull($personOutput->jobTitle);
            } elseif ($uuid2 === $personOutput->uuid) {
                $this->assertEquals($uuid2, $personOutput->uuid);
                $this->assertEquals($familyName2, $personOutput->familyName);
                $this->assertEquals($givenName2, $personOutput->givenName);
                $this->assertEquals($email2, $personOutput->email);
                $this->assertEquals($gender2, $personOutput->gender);
                $this->assertNull($personOutput->telephone);
                $this->assertNull($personOutput->addressStreet);
                $this->assertNull($personOutput->addressPostcode);
                $this->assertNull($personOutput->addressLocality);
                $this->assertNull($personOutput->addressCountry);
                $this->assertNull($personOutput->birthDate);
                $this->assertNull($personOutput->jobTitle);
            }
        }
    }
}
