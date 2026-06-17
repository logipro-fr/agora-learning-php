<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerFreeOutput;
use AgoraLearningPhp\Enum\Gender;

class TrainerTest extends AbstractAgoraLearningTest
{
    public function testCreateCompleteTrainerEmployee(): void
    {
        //Arrange - créer une société en dépendance
        $societyInput = new SocietyInput('Société Test Formateur Employé');
        $societyOutput = $this->client->createSociety($societyInput);

        $familyName = 'Martin';
        $givenName = 'Sophie';
        $email = 'sophie.martin.complet@example.com';
        $visibleInformation = true;
        $notifyUser = true;
        $gender = Gender::GENDER_FEMALE;
        $telephone = '0123456789';
        $mobileNumber = '0612345678';
        $addressStreet = '12 avenue des Formateurs';
        $addressPostcode = '69001';
        $addressLocality = 'Lyon';
        $addressCountry = 'France';
        $image = 'https://www.agora-learning.com/apiAgoraLearning/ressources/femme.jpg';
        $birthDate = new \DateTimeImmutable('1990-03-20');
        $jobTitle = 'Formatrice senior';
        $cv = 'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf';
        $degree = 'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf';
        $contract = 'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf';
        $jobDescription = 'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf';

        $trainerInput = new TrainerEmployeeInput(
            $familyName,
            $givenName,
            $email,
            $visibleInformation,
            $notifyUser,
            $gender,
            $telephone,
            $mobileNumber,
            $addressStreet,
            $addressPostcode,
            $addressLocality,
            $addressCountry,
            $image,
            $birthDate,
            $jobTitle,
            $societyOutput->uuid,
            $cv,
            $degree,
            $contract,
            $jobDescription
        );

        //Act
        $trainerOutput = $this->client->createTrainerEmployee($trainerInput);

        // Assert
        $this->assertEquals($familyName, $trainerOutput->familyName);
        $this->assertEquals($givenName, $trainerOutput->givenName);
        $this->assertEquals($email, $trainerOutput->email);
        $this->assertEquals($visibleInformation, $trainerOutput->visibleInformation);
        $this->assertEquals($gender, $trainerOutput->gender);
        $this->assertEquals($telephone, $trainerOutput->telephone);
        $this->assertEquals($mobileNumber, $trainerOutput->mobileNumber);
        $this->assertEquals($addressStreet, $trainerOutput->addressStreet);
        $this->assertEquals($addressPostcode, $trainerOutput->addressPostcode);
        $this->assertEquals($addressLocality, $trainerOutput->addressLocality);
        $this->assertEquals($addressCountry, $trainerOutput->addressCountry);
        $this->assertEquals($birthDate, $trainerOutput->birthDate);
        $this->assertEquals($jobTitle, $trainerOutput->jobTitle);
        $this->assertNotNull($trainerOutput->society);
        $this->assertEquals($societyOutput->uuid, $trainerOutput->society->uuid);
    }

    public function testCreateMinimumTrainerEmployee(): void
    {
        //Arrange
        $familyName = 'Durand';
        $givenName = 'Pierre';
        $email = 'pierre.durand.min@example.com';
        $trainerInput = new TrainerEmployeeInput(
            $familyName,
            $givenName,
            $email
        );

        //Act
        $trainerOutput = $this->client->createTrainerEmployee($trainerInput);

        // Assert
        $this->assertEquals($familyName, $trainerOutput->familyName);
        $this->assertEquals($givenName, $trainerOutput->givenName);
        $this->assertEquals($email, $trainerOutput->email);
        $this->assertNull($trainerOutput->telephone);
        $this->assertNull($trainerOutput->mobileNumber);
        $this->assertNull($trainerOutput->addressStreet);
        $this->assertNull($trainerOutput->addressPostcode);
        $this->assertNull($trainerOutput->addressLocality);
        $this->assertNull($trainerOutput->addressCountry);
        $this->assertNull($trainerOutput->birthDate);
        $this->assertNull($trainerOutput->jobTitle);
        $this->assertNull($trainerOutput->society);
    }

    public function testCreateCompleteTrainerFree(): void
    {
        //Arrange - créer une société en dépendance
        $societyInput = new SocietyInput('Société Test Formateur Libre');
        $societyOutput = $this->client->createSociety($societyInput);

        $familyName = 'Bernard';
        $givenName = 'Pierre';
        $email = 'pierre.bernard.complet@example.com';
        $gender = Gender::GENDER_MALE;
        $telephone = '0123456789';
        $mobileNumber = '0612345678';
        $addressStreet = '5 rue des Indépendants';
        $addressPostcode = '33000';
        $addressLocality = 'Bordeaux';
        $addressCountry = 'France';
        $image = 'https://www.agora-learning.com/apiAgoraLearning/ressources/homme.jpg';
        $birthDate = new \DateTimeImmutable('1982-11-08');
        $jobTitle = 'Formateur indépendant';
        $cv = 'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf';
        $degree = 'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf';
        $contract = 'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf';
        $jobDescription = 'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf';
        $hourlyCost = 80.0;
        $daylyCost = 600.0;
        $siret = '12345678901234';
        $tvaNumber = 'FR12345678901';
        $urssafNumber = '987654321';
        $urssafCertificate = 'https://www.agora-learning.com/apiAgoraLearning/ressources/a-pdf-test.pdf';
        $billingAddressStreet = '5 rue des Indépendants';
        $billingAddressPostcode = '33000';
        $billingAddressLocality = 'Bordeaux';
        $billingAddressCountry = 'France';

        $trainerInput = new TrainerFreeInput(
            $familyName,
            $givenName,
            $email,
            true,
            true,
            $gender,
            $telephone,
            $mobileNumber,
            $addressStreet,
            $addressPostcode,
            $addressLocality,
            $addressCountry,
            $image,
            $birthDate,
            $jobTitle,
            $societyOutput->uuid,
            $cv,
            $degree,
            $contract,
            $jobDescription,
            $hourlyCost,
            $daylyCost,
            $siret,
            $tvaNumber,
            $urssafNumber,
            $urssafCertificate,
            $billingAddressStreet,
            $billingAddressPostcode,
            $billingAddressLocality,
            $billingAddressCountry
        );

        //Act
        $trainerOutput = $this->client->createTrainerFree($trainerInput);

        // Assert
        $this->assertInstanceOf(TrainerFreeOutput::class, $trainerOutput);
        /** @var TrainerFreeOutput $trainerOutput */
        $this->assertEquals($familyName, $trainerOutput->familyName);
        $this->assertEquals($givenName, $trainerOutput->givenName);
        $this->assertEquals($email, $trainerOutput->email);
        $this->assertEquals($gender, $trainerOutput->gender);
        $this->assertEquals($telephone, $trainerOutput->telephone);
        $this->assertEquals($mobileNumber, $trainerOutput->mobileNumber);
        $this->assertEquals($addressStreet, $trainerOutput->addressStreet);
        $this->assertEquals($addressPostcode, $trainerOutput->addressPostcode);
        $this->assertEquals($addressLocality, $trainerOutput->addressLocality);
        $this->assertEquals($addressCountry, $trainerOutput->addressCountry);
        $this->assertEquals($birthDate, $trainerOutput->birthDate);
        $this->assertEquals($jobTitle, $trainerOutput->jobTitle);
        $this->assertNotNull($trainerOutput->society);
        $this->assertEquals($societyOutput->uuid, $trainerOutput->society->uuid);
        $this->assertEquals($hourlyCost, $trainerOutput->hourlyCost);
        $this->assertEquals($daylyCost, $trainerOutput->daylyCost);
        $this->assertEquals($siret, $trainerOutput->siret);
        $this->assertEquals($tvaNumber, $trainerOutput->tvaNumber);
        $this->assertEquals($urssafNumber, $trainerOutput->urssafNumber);
        $this->assertEquals($billingAddressStreet, $trainerOutput->billingAddressStreet);
        $this->assertEquals($billingAddressPostcode, $trainerOutput->billingAddressPostcode);
        $this->assertEquals($billingAddressLocality, $trainerOutput->billingAddressLocality);
        $this->assertEquals($billingAddressCountry, $trainerOutput->billingAddressCountry);
    }

    public function testCreateMinimumTrainerFree(): void
    {
        //Arrange
        $familyName = 'Leroy';
        $givenName = 'Marc';
        $email = 'marc.leroy.min@example.com';
        $trainerInput = new TrainerFreeInput(
            $familyName,
            $givenName,
            $email
        );

        //Act
        $trainerOutput = $this->client->createTrainerFree($trainerInput);

        // Assert
        $this->assertInstanceOf(TrainerFreeOutput::class, $trainerOutput);
        /** @var TrainerFreeOutput $trainerOutput */
        $this->assertEquals($familyName, $trainerOutput->familyName);
        $this->assertEquals($givenName, $trainerOutput->givenName);
        $this->assertEquals($email, $trainerOutput->email);
        $this->assertNull($trainerOutput->telephone);
        $this->assertNull($trainerOutput->mobileNumber);
        $this->assertNull($trainerOutput->addressStreet);
        $this->assertNull($trainerOutput->addressPostcode);
        $this->assertNull($trainerOutput->addressLocality);
        $this->assertNull($trainerOutput->addressCountry);
        $this->assertNull($trainerOutput->birthDate);
        $this->assertNull($trainerOutput->jobTitle);
        $this->assertNull($trainerOutput->society);
        $this->assertNull($trainerOutput->hourlyCost);
        $this->assertNull($trainerOutput->daylyCost);
        $this->assertNull($trainerOutput->siret);
        $this->assertNull($trainerOutput->tvaNumber);
        $this->assertNull($trainerOutput->urssafNumber);
        $this->assertNull($trainerOutput->billingAddressStreet);
        $this->assertNull($trainerOutput->billingAddressPostcode);
        $this->assertNull($trainerOutput->billingAddressLocality);
        $this->assertNull($trainerOutput->billingAddressCountry);
    }

    public function testGetTrainer(): void
    {
        //Arrange
        $familyName = 'Moreau';
        $givenName = 'Lucas';
        $email = 'lucas.moreau.get@example.com';
        $trainerInput = new TrainerEmployeeInput(
            $familyName,
            $givenName,
            $email
        );
        $output = $this->client->createTrainerEmployee($trainerInput);
        $uuid = $output->uuid;

        //Act
        $trainerOutput = $this->client->getTrainer($uuid);

        // Assert
        $this->assertEquals($uuid, $trainerOutput->uuid);
        $this->assertEquals($familyName, $trainerOutput->familyName);
        $this->assertEquals($givenName, $trainerOutput->givenName);
        $this->assertEquals($email, $trainerOutput->email);
    }

    public function testGetCollectionTrainer(): void
    {
        //Arrange
        $familyName1 = 'TrainerUn';
        $givenName1 = 'Jean';
        $email1 = 'trainer.un.coll@example.com';
        $trainerInput1 = new TrainerEmployeeInput($familyName1, $givenName1, $email1);
        $output1 = $this->client->createTrainerEmployee($trainerInput1);
        $uuid1 = $output1->uuid;

        $familyName2 = 'TrainerDeux';
        $givenName2 = 'Jean';
        $email2 = 'trainer.deux.coll@example.com';
        $trainerInput2 = new TrainerEmployeeInput($familyName2, $givenName2, $email2);
        $output2 = $this->client->createTrainerEmployee($trainerInput2);
        $uuid2 = $output2->uuid;

        //Act
        $trainerOutputs = $this->client->getCollectionTrainer();

        // Assert
        foreach ($trainerOutputs as $trainerOutput) {
            if ($uuid1 === $trainerOutput->uuid) {
                $this->assertEquals($uuid1, $trainerOutput->uuid);
                $this->assertEquals($familyName1, $trainerOutput->familyName);
                $this->assertEquals($givenName1, $trainerOutput->givenName);
                $this->assertEquals($email1, $trainerOutput->email);
            } elseif ($uuid2 === $trainerOutput->uuid) {
                $this->assertEquals($uuid2, $trainerOutput->uuid);
                $this->assertEquals($familyName2, $trainerOutput->familyName);
                $this->assertEquals($givenName2, $trainerOutput->givenName);
                $this->assertEquals($email2, $trainerOutput->email);
            }
        }
    }
}
