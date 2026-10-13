<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeePatchInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreePatchInput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerFreeOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\AgoraId;

class TrainerTest extends AbstractAgoraLearningTest
{
    public function testCreateCompleteTrainerEmployee(): void
    {
        //Arrange - créer une société en dépendance
        $societyInput = new SocietyInput($this->newExternalId('SOC'), 'Société Test Formateur Employé');
        $societyCreated = $this->client->createSociety($societyInput);

        $familyName = 'Martin';
        $givenName = 'Sophie';
        $email = $this->newEmail('sophie.martin.complet');
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
            $this->newExternalId('T'),
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
            $societyCreated->agoraId->getId(),
            $cv,
            $degree,
            $contract,
            $jobDescription
        );

        //Act
        $created = $this->client->createTrainerEmployee($trainerInput);
        $trainerOutput = $this->client->getTrainer($created->agoraId);

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
        $this->assertEquals($societyCreated->agoraId->getId(), $trainerOutput->society->uuid);
    }

    public function testCreateMinimumTrainerEmployee(): void
    {
        //Arrange
        $familyName = 'Durand';
        $givenName = 'Pierre';
        $email = $this->newEmail('pierre.durand.min');
        $trainerInput = new TrainerEmployeeInput(
            $this->newExternalId('T'),
            $familyName,
            $givenName,
            $email
        );

        //Act
        $created = $this->client->createTrainerEmployee($trainerInput);
        $trainerOutput = $this->client->getTrainer($created->agoraId);

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
        $societyInput = new SocietyInput($this->newExternalId('SOC'), 'Société Test Formateur Libre');
        $societyCreated = $this->client->createSociety($societyInput);

        $familyName = 'Bernard';
        $givenName = 'Pierre';
        $email = $this->newEmail('pierre.bernard.complet');
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
            $this->newExternalId('T'),
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
            $societyCreated->agoraId->getId(),
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
        $created = $this->client->createTrainerFree($trainerInput);
        $trainerOutput = $this->client->getTrainer($created->agoraId);

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
        $this->assertEquals($societyCreated->agoraId->getId(), $trainerOutput->society->uuid);
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
        $email = $this->newEmail('marc.leroy.min');
        $trainerInput = new TrainerFreeInput(
            $this->newExternalId('T'),
            $familyName,
            $givenName,
            $email
        );

        //Act
        $created = $this->client->createTrainerFree($trainerInput);
        $trainerOutput = $this->client->getTrainer($created->agoraId);

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
        $email = $this->newEmail('lucas.moreau.get');
        $trainerInput = new TrainerEmployeeInput(
            $this->newExternalId('T'),
            $familyName,
            $givenName,
            $email
        );
        $uuid = $this->client->createTrainerEmployee($trainerInput)->agoraId->getId();

        //Act
        $trainerOutput = $this->client->getTrainer(new AgoraId($uuid));

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
        $email1 = $this->newEmail('trainer.un.coll');
        $trainerInput1 = new TrainerEmployeeInput($this->newExternalId('T'), $familyName1, $givenName1, $email1);
        $uuid1 = $this->client->createTrainerEmployee($trainerInput1)->agoraId->getId();

        $familyName2 = 'TrainerDeux';
        $givenName2 = 'Jean';
        $email2 = $this->newEmail('trainer.deux.coll');
        $trainerInput2 = new TrainerEmployeeInput($this->newExternalId('T'), $familyName2, $givenName2, $email2);
        $uuid2 = $this->client->createTrainerEmployee($trainerInput2)->agoraId->getId();

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

    public function testReplaceTrainerEmployee(): void
    {
        //Arrange
        $externalId = $this->newExternalId('T');
        $email = $this->newEmail('trainer.replace');
        $this->client->createTrainerEmployee(new TrainerEmployeeInput($externalId, 'Avant', 'Pierre', $email));

        //Act
        $this->client->replaceTrainerEmployee(
            $externalId,
            new TrainerEmployeeInput($externalId, 'Apres', 'Pierre', $email, false)
        );
        $trainerOutput = $this->client->getTrainer($externalId);

        // Assert
        $this->assertEquals('Apres', $trainerOutput->familyName);
        $this->assertFalse($trainerOutput->visibleInformation);
    }

    public function testPatchTrainerEmployee(): void
    {
        //Arrange
        $created = $this->client->createTrainerEmployee(new TrainerEmployeeInput(
            $this->newExternalId('T'),
            'Patch',
            'Pierre',
            $this->newEmail('trainer.patch')
        ));

        //Act
        $this->client->patchTrainerEmployee($created->agoraId, (new TrainerEmployeePatchInput())->setJobTitle('Formateur'));
        $trainerOutput = $this->client->getTrainer($created->agoraId);

        // Assert
        $this->assertEquals('Patch', $trainerOutput->familyName);
        $this->assertEquals('Formateur', $trainerOutput->jobTitle);
    }

    public function testPatchTrainerFree(): void
    {
        //Arrange
        $created = $this->client->createTrainerFree(new TrainerFreeInput(
            $this->newExternalId('T'),
            'Patch',
            'Sophie',
            $this->newEmail('trainer.free.patch')
        ));

        //Act
        $this->client->patchTrainerFree($created->agoraId, (new TrainerFreePatchInput())->setHourlyCost(55.0));
        $trainerOutput = $this->client->getTrainer($created->agoraId);

        // Assert
        $this->assertInstanceOf(TrainerFreeOutput::class, $trainerOutput);
        $this->assertEquals(55.0, $trainerOutput->hourlyCost);
    }
}
