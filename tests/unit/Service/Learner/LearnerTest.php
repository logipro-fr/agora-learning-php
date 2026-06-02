<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Learner;

use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Learner\Learner;
use AgoraLearningPhp\Tests\unit\Service\ServiceTestCase;

class LearnerTest extends ServiceTestCase
{
    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    private function makeMinimalData(): array
    {
        return [
            'id'           => 'learner-uuid-001',
            'username'     => 'jdupont',
            'familyName'   => 'Dupont',
            'givenName'    => 'Jean',
            'recoverEmail' => 'jean@example.com',
            'email' => 'jean@example.com',
        ];
    }

    private function makeLearner(HttpClient $httpClient): Learner
    {
        return new class($httpClient) extends Learner {
            public function __construct(HttpClient $client)
            {
                $this->httpClient = $client;
            }
        };
    }

    // -------------------------------------------------------------------------
    // convertDataToDTO — logique pure, sans réseau
    // -------------------------------------------------------------------------

    public function testConvertDataToDTOReturnsLearnerOutput(): void
    {
        // Act
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertInstanceOf(LearnerOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        // Act
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertSame('learner-uuid-001', $dto->uuid);
        $this->assertSame('jdupont', $dto->username);
        $this->assertSame('Dupont', $dto->familyName);
        $this->assertSame('Jean', $dto->givenName);
        $this->assertSame('jean@example.com', $dto->recoverEmail);
    }

    public function testConvertDataToDTOSetsGenderNaByDefault(): void
    {
        // Act
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertSame(Gender::GENDER_NA, $dto->gender);
    }

    public function testConvertDataToDTOSetsOptionalFieldsToNullWhenAbsent(): void
    {
        // Act
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        // Assert        
        $this->assertNull($dto->telephone);
        $this->assertNull($dto->addressStreet);
        $this->assertNull($dto->addressPostcode);
        $this->assertNull($dto->addressLocality);
        $this->assertNull($dto->addressCountry);
        $this->assertNull($dto->birthDate);
        $this->assertNull($dto->jobTitle);
    }

    public function testConvertDataToDTOMapsGender(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), ['gender' => Gender::GENDER_MALE]);

        // Act
        $dto = Learner::convertDataToDTO($data);

        // Assert
        $this->assertSame(Gender::GENDER_MALE, $dto->gender);
    }

    public function testConvertDataToDTOParsesBirthDate(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), ['birthDate' => '1985-07-14T00:00:00+00:00']);

        // Act
        $dto = Learner::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->birthDate);
        $this->assertSame('1985-07-14', $dto->birthDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOReturnsNullBirthDateWhenFieldIsNull(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), ['birthDate' => null]);

        // Act
        $dto = Learner::convertDataToDTO($data);

        // Assert
        $this->assertNull($dto->birthDate);
    }

    public function testConvertDataToDTOMapsAllOptionalFields(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), [
            'gender'          => Gender::GENDER_MALE,
            'email'           => 'jean.work@example.com',
            'telephone'       => '+33611223344',
            'addressStreet'   => '1 rue Test',
            'addressPostcode' => '75001',
            'addressLocality' => 'Paris',
            'addressCountry'  => 'FR',
            'image'           => 'http://img.example.com/jean.jpg',
            'birthDate'       => '1985-07-14T00:00:00+00:00',
            'jobTitle'        => 'Ingénieur',
        ]);

        // Act
        $dto = Learner::convertDataToDTO($data);

        // Assert
        $this->assertSame('jean.work@example.com', $dto->email);
        $this->assertSame('+33611223344', $dto->telephone);
        $this->assertSame('1 rue Test', $dto->addressStreet);
        $this->assertSame('75001', $dto->addressPostcode);
        $this->assertSame('Paris', $dto->addressLocality);
        $this->assertSame('FR', $dto->addressCountry);
        $this->assertSame('1985-07-14', $dto->birthDate->format('Y-m-d'));
        $this->assertSame('Ingénieur', $dto->jobTitle);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionLearnerReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        // Arrange
        $learner = $this->makeLearner($this->makeHttpClientWithResponse(200, '[]'));

        // Act
        $result = $learner->getCollectionLearner();

        // Assert
        $this->assertSame([], $result);
    }

    public function testGetCollectionLearnerReturnsDTOArray(): void
    {
        // Arrange
        $body = json_encode([
            array_merge($this->makeMinimalData(), ['id' => 'l1', 'username' => 'user1']),
            array_merge($this->makeMinimalData(), ['id' => 'l2', 'username' => 'user2']),
        ]);
        $learner = $this->makeLearner($this->makeHttpClientWithResponse(200, $body));

        // Act
        $result = $learner->getCollectionLearner();

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(LearnerOutput::class, $result[0]);
        $this->assertSame('l1', $result[0]->uuid);
    }

    public function testGetLearnerReturnsSingleDTO(): void
    {
        // Arrange
        $learner = $this->makeLearner(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalData()))
        );

        // Act
        $result = $learner->getLearner('learner-uuid-001');

        // Assert
        $this->assertInstanceOf(LearnerOutput::class, $result);
        $this->assertSame('learner-uuid-001', $result->uuid);
    }

    public function testGetLearnerThrowsExceptionWhenApiReturns404(): void
    {
        // Arrange
        $learner = $this->makeLearner(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Not Found']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Not Found');

        // Act
        $learner->getLearner('missing-uuid');
    }

    public function testGetLearnerThrowsExceptionWithDefaultMessageWhenNoDetail(): void
    {
        // Arrange
        $learner = $this->makeLearner(
            $this->makeHttpClientWithResponse(500, json_encode([]))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('An unexpected error occured');

        // Act
        $learner->getLearner('some-uuid');
    }

    public function testPostLearnerWithBirthDate(): void
    {
        // Arrange
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'learner-new-001', 'username' => 'new_user']);
        $learner      = $this->makeLearner(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );
        $input = new LearnerInput(
            'Nouveau',
            'Apprenant',
            'new@example.com',
            'new@example.com',
            Gender::GENDER_NA,
            null,
            null,
            null,
            null,
            null,
            null,
            new \DateTimeImmutable('2000-01-01'),
            'Stagiaire'
        );

        // Act
        $result = $learner->postLearner($input);

        // Assert
        $this->assertInstanceOf(LearnerOutput::class, $result);
        $this->assertSame('learner-new-001', $result->uuid);
    }

    public function testPostLearnerWithoutBirthDate(): void
    {
        // Arrange
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'learner-new-002', 'username' => 'user2']);
        $learner      = $this->makeLearner(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        // Act
        $result = $learner->postLearner(new LearnerInput(
            'Test',
            'User',
            'u@u.com',
            'u@u.com'
        ));

        // Assert
        $this->assertInstanceOf(LearnerOutput::class, $result);
    }

    public function testPostLearnerSendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'learner-new-003', 'username' => 'u3']);
        $learner      = $this->makeLearner(
            $this->makeHttpClientInspectingBody(200, json_encode($responseData), $capturedBody)
        );
        $input = new LearnerInput(
            'Dupont',
            'Jean',
            'jean@example.com',
            'work@example.com',
            Gender::GENDER_MALE,
            '+33600000000',
            '1 rue Test',
            '75001',
            'Paris',
            'FR',
            'http://img.example.com/jean.jpg',
            new \DateTimeImmutable('1990-01-01'),
            'Développeur'
        );

        // Act
        $learner->postLearner($input);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame('Dupont', $body['familyName']);
        $this->assertSame('Jean', $body['givenName']);
        $this->assertSame('jean@example.com', $body['recoverEmail']);
        $this->assertSame('work@example.com', $body['email']);
        $this->assertSame(Gender::GENDER_MALE, $body['gender']);
        $this->assertArrayHasKey('birthDate', $body);
        $this->assertSame('Développeur', $body['jobTitle']);
        $this->assertSame('+33600000000', $body['telephone']);
        $this->assertSame('1 rue Test', $body['addressStreet']);
        $this->assertSame('75001', $body['addressPostcode']);
        $this->assertSame('Paris', $body['addressLocality']);
        $this->assertSame('FR', $body['addressCountry']);
    }
}
