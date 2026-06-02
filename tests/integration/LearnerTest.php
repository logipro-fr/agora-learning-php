<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\Enum\Gender;

class LearnerTest extends AbstractAgoraLearningTest
{
    public function testCreateCompleteLearner(): void
    {
        //Arrange
        $familyName = 'Leblanc';
        $givenName = 'Alice';
        $recoverEmail = 'alice.leblanc.recovery@example.com';
        $gender = Gender::GENDER_FEMALE;
        $email = 'alice.leblanc@example.com';
        $telephone = '0123456789';
        $addressStreet = '8 rue des Apprenants';
        $addressPostcode = '13001';
        $addressLocality = 'Marseille';
        $addressCountry = 'France';
        $image = 'https://www.agora-learning.com/apiAgoraLearning/ressources/femme.jpg';
        $birthDate = new \DateTimeImmutable('1995-07-22');
        $jobTitle = 'Chargée de projet';

        $learnerInput = new LearnerInput(
            $familyName,
            $givenName,
            $recoverEmail,
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
        $learnerOutput = $this->client->createLearner($learnerInput);

        // Assert
        $this->assertEquals($familyName, $learnerOutput->familyName);
        $this->assertEquals($givenName, $learnerOutput->givenName);
        $this->assertEquals($gender, $learnerOutput->gender);
        $this->assertEquals($recoverEmail, $learnerOutput->recoverEmail);
        $this->assertEquals($email, $learnerOutput->email);
        $this->assertEquals($telephone, $learnerOutput->telephone);
        $this->assertEquals($addressStreet, $learnerOutput->addressStreet);
        $this->assertEquals($addressPostcode, $learnerOutput->addressPostcode);
        $this->assertEquals($addressLocality, $learnerOutput->addressLocality);
        $this->assertEquals($addressCountry, $learnerOutput->addressCountry);
        $this->assertEquals($birthDate, $learnerOutput->birthDate);
        $this->assertEquals($jobTitle, $learnerOutput->jobTitle);
    }

    public function testCreateMinimumLearner(): void
    {
        //Arrange
        $familyName = 'Palin';
        $givenName = 'Matt';
        $recoverEmail = 'matt.palin.recovery2@example.com';

        $learnerInput = new LearnerInput(
            $familyName,
            $givenName,
            $recoverEmail,
            $recoverEmail
        );

        //Act
        $learnerOutput = $this->client->createLearner($learnerInput);

        // Assert
        $this->assertEquals($familyName, $learnerOutput->familyName);
        $this->assertEquals($givenName, $learnerOutput->givenName);
        $this->assertEquals($recoverEmail, $learnerOutput->recoverEmail);
        $this->assertEquals($recoverEmail, $learnerOutput->email);
        $this->assertNull($learnerOutput->telephone);
        $this->assertNull($learnerOutput->addressStreet);
        $this->assertNull($learnerOutput->addressPostcode);
        $this->assertNull($learnerOutput->addressLocality);
        $this->assertNull($learnerOutput->addressCountry);
        $this->assertNull($learnerOutput->birthDate);
        $this->assertNull($learnerOutput->jobTitle);
    }

    public function testGetLearner(): void
    {
        //Arrange
        $familyName = 'Garcia';
        $givenName = 'Ana';
        $recoverEmail = 'ana.garcia.get@example.com';
        $learnerInput = new LearnerInput(
            $familyName,
            $givenName,
            $recoverEmail,
            $recoverEmail
        );
        $output = $this->client->createLearner($learnerInput);
        $uuid = $output->uuid;

        //Act
        $learnerOutput = $this->client->getLearner($uuid);

        // Assert
        $this->assertEquals($uuid, $learnerOutput->uuid);
        $this->assertEquals($familyName, $learnerOutput->familyName);
        $this->assertEquals($givenName, $learnerOutput->givenName);
        $this->assertEquals($recoverEmail, $learnerOutput->recoverEmail);
    }

    public function testGetCollectionLearner(): void
    {
        //Arrange
        $familyName1 = 'LearnerUn';
        $givenName1 = 'Jean';
        $recoverEmail1 = 'learner.un.coll@example.com';
        $learnerInput1 = new LearnerInput($familyName1, $givenName1, $recoverEmail1, $recoverEmail1);
        $output1 = $this->client->createLearner($learnerInput1);
        $uuid1 = $output1->uuid;

        $familyName2 = 'LearnerDeux';
        $givenName2 = 'Jean';
        $recoverEmail2 = 'learner.deux.coll@example.com';
        $learnerInput2 = new LearnerInput($familyName2, $givenName2, $recoverEmail2, $recoverEmail2);
        $output2 = $this->client->createLearner($learnerInput2);
        $uuid2 = $output2->uuid;

        //Act
        $learnerOutputs = $this->client->getCollectionLearner();

        // Assert
        foreach ($learnerOutputs as $learnerOutput) {
            if ($uuid1 === $learnerOutput->uuid) {
                $this->assertEquals($uuid1, $learnerOutput->uuid);
                $this->assertEquals($familyName1, $learnerOutput->familyName);
                $this->assertEquals($givenName1, $learnerOutput->givenName);
                $this->assertEquals($recoverEmail1, $learnerOutput->recoverEmail);
                $this->assertEquals($recoverEmail1, $learnerOutput->email);
            } elseif ($uuid2 === $learnerOutput->uuid) {
                $this->assertEquals($uuid2, $learnerOutput->uuid);
                $this->assertEquals($familyName2, $learnerOutput->familyName);
                $this->assertEquals($givenName2, $learnerOutput->givenName);
                $this->assertEquals($recoverEmail2, $learnerOutput->recoverEmail);
                $this->assertEquals($recoverEmail2, $learnerOutput->email);
            }
        }
    }
}
