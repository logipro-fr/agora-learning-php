<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Learner;

use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Input\Learner\LearnerPatchInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;
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
            'agoraId'      => 'learner-uuid-001',
            'username'     => 'jdupont',
            'familyName'   => 'Dupont',
            'givenName'    => 'Jean',
            'recoverEmail' => 'jean@example.com',
            'email' => 'jean@example.com',
        ];
    }

    private function makeLearner(HttpClient $httpClient): Learner
    {
        return new class ($httpClient) extends Learner {
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

    public function testConvertDataToDTOMapsEmbeddedSociety(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), [
            'society' => [
                'agoraId' => 'soc-uuid-001',
                'name'  => 'Société Test',
            ],
        ]);

        // Act
        $dto = Learner::convertDataToDTO($data);

        // Assert
        $this->assertNotNull($dto->society);
        $this->assertSame('soc-uuid-001', $dto->society->uuid);
    }

    public function testConvertDataToDTOReturnsNullSocietyWhenSocietyIsNull(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), ['society' => null]);

        // Act
        $dto = Learner::convertDataToDTO($data);

        // Assert
        $this->assertNull($dto->society);
    }

    public function testConvertDataToDTOReturnsNullSocietyWhenSocietyKeyAbsent(): void
    {
        // Act
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertNull($dto->society);
    }

    public function testConvertDataToDTOMapsExternalIdentifier(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), [
            'externalIdentifier' => ['sourceName' => 'MonERP', 'id' => 'X-42'],
        ]);

        // Act
        $dto = Learner::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(ExternalId::class, $dto->externalIdentifier);
        $this->assertSame('MonERP:X-42', $dto->externalIdentifier->getId());
    }

    public function testConvertDataToDTOSetsExternalIdentifierToNullWhenAbsent(): void
    {
        // Act
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertNull($dto->externalIdentifier);
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
            array_merge($this->makeMinimalData(), ['agoraId' => 'l1', 'username' => 'user1']),
            array_merge($this->makeMinimalData(), ['agoraId' => 'l2', 'username' => 'user2']),
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
        $result = $learner->getLearner(new AgoraId('learner-uuid-001'));

        // Assert
        $this->assertInstanceOf(LearnerOutput::class, $result);
        $this->assertSame('learner-uuid-001', $result->uuid);
    }

    public function testGetLearnerWithExternalIdCallsEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $learner = $this->makeLearner($this->makeHttpClientInspectingRequest(
            200,
            json_encode($this->makeMinimalData()),
            $method,
            $url
        ));

        // Act
        $result = $learner->getLearner(new ExternalId('Src', 'X-42'));

        // Assert
        $this->assertSame('GET', $method);
        $this->assertSame('https://api.test.local/api/external/v1/learners/Src%3AX-42', $url);
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
        $learner->getLearner(new AgoraId('missing-uuid'));
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
        $learner->getLearner(new AgoraId('some-uuid'));
    }

    public function testPostLearnerWithBirthDate(): void
    {
        // Arrange
        $responseData = ['agoraId' => 'per_new_001', 'sourceName' => 'Test', 'externalId' => 'L-1'];
        $learner      = $this->makeLearner(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );
        $input = new LearnerInput(
            new ExternalId('Test', 'L-1'),
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
        $this->assertInstanceOf(CreatedOutput::class, $result);
        $this->assertSame('per_new_001', $result->agoraId->getId());
        $this->assertNotNull($result->externalId);
        $this->assertSame('Test:L-1', $result->externalId->getId());
    }

    public function testPostLearnerWithoutBirthDate(): void
    {
        // Arrange
        $responseData = ['agoraId' => 'per_new_002', 'sourceName' => 'Test', 'externalId' => 'L-2'];
        $learner      = $this->makeLearner(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        // Act
        $result = $learner->postLearner(new LearnerInput(
            new ExternalId('Test', 'L-2'),
            'Test',
            'User',
            'u@u.com',
            'u@u.com'
        ));

        // Assert
        $this->assertInstanceOf(CreatedOutput::class, $result);
    }

    public function testPostLearnerSendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $responseData = ['agoraId' => 'per_new_003', 'sourceName' => 'Test', 'externalId' => 'L-3'];
        $learner      = $this->makeLearner(
            $this->makeHttpClientInspectingBody(200, json_encode($responseData), $capturedBody)
        );
        $input = new LearnerInput(
            new ExternalId('Test', 'L-3'),
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

    public function testPostLearnerSendsSocietyId(): void
    {
        // Arrange
        $capturedBody = '';
        $responseData = ['agoraId' => 'per_new_004', 'sourceName' => 'Test', 'externalId' => 'L-4'];
        $learner      = $this->makeLearner(
            $this->makeHttpClientInspectingBody(200, json_encode($responseData), $capturedBody)
        );
        $input = new LearnerInput(
            new ExternalId('Test', 'L-4'),
            'Dupont',
            'Jean',
            'jean@example.com',
            'jean@example.com',
            Gender::GENDER_NA,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            'Test:S-1'
        );

        // Act
        $learner->postLearner($input);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame('Test:S-1', $body['societyId']);
    }

    public function testPostLearnerSendsNullSocietyIdByDefault(): void
    {
        // Arrange
        $capturedBody = '';
        $responseData = ['agoraId' => 'per_new_005', 'sourceName' => 'Test', 'externalId' => 'L-5'];
        $learner      = $this->makeLearner(
            $this->makeHttpClientInspectingBody(200, json_encode($responseData), $capturedBody)
        );
        $input = new LearnerInput(new ExternalId('Test', 'L-5'), 'Dupont', 'Jean', 'jean@example.com', 'jean@example.com');

        // Act
        $learner->postLearner($input);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertArrayHasKey('societyId', $body);
        $this->assertNull($body['societyId']);
    }

    // -------------------------------------------------------------------------
    // PUT / PATCH
    // -------------------------------------------------------------------------

    public function testPutLearnerSendsPutRequestWithFullBody(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $capturedBody = '';
        $learner = $this->makeLearner($this->makeHttpClientInspectingRequest(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $method, $url));
        $learnerBody = $this->makeLearner($this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $capturedBody));
        $input = new LearnerInput(new ExternalId('Src', 'X-42'), 'Martin', 'Claire', 'recover@example.net', 'claire@example.net');

        // Act
        $learner->putLearner(new ExternalId('Src', 'X-42'), $input);
        $learnerBody->putLearner(new ExternalId('Src', 'X-42'), $input);

        // Assert
        $this->assertSame('PUT', $method);
        $this->assertSame('https://api.test.local/api/external/v1/learners/Src%3AX-42', $url);
        $body = json_decode($capturedBody, true);
        $this->assertSame('recover@example.net', $body['recoverEmail']);
        $this->assertArrayHasKey('mobileNumber', $body);
    }

    public function testPatchLearnerSendsOnlySetFields(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $capturedBody = '';
        $learner = $this->makeLearner($this->makeHttpClientInspectingRequest(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $method, $url));
        $learnerBody = $this->makeLearner($this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $capturedBody));
        $patch = (new LearnerPatchInput())->setRecoverEmail('new@example.net')->setMobileNumber(null);

        // Act
        $learner->patchLearner(new AgoraId('per_001'), $patch);
        $learnerBody->patchLearner(new AgoraId('per_001'), $patch);

        // Assert
        $this->assertSame('PATCH', $method);
        $this->assertSame('https://api.test.local/api/external/v1/learners/per_001', $url);
        $this->assertSame(['recoverEmail' => 'new@example.net', 'mobileNumber' => null], json_decode($capturedBody, true));
    }

    public function testPatchLearnerSendsSocietyIdNullToRemoveRelation(): void
    {
        // Arrange
        $capturedBody = '';
        $learner = $this->makeLearner($this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $capturedBody));
        $patch = (new LearnerPatchInput())->setSocietyId(null);

        // Act
        $learner->patchLearner(new AgoraId('per_001'), $patch);

        // Assert
        $this->assertSame(['societyId' => null], json_decode($capturedBody, true));
    }

    // -------------------------------------------------------------------------
    // deleteLearner
    // -------------------------------------------------------------------------

    public function testDeleteLearnerWithExternalIdSendsDeleteToEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $learner = $this->makeLearner($this->makeHttpClientInspectingRequest(204, '', $method, $url));

        // Act
        $learner->deleteLearner(new ExternalId('Src', 'X-42'));

        // Assert
        $this->assertSame('DELETE', $method);
        $this->assertSame('https://api.test.local/api/external/v1/learners/Src%3AX-42', $url);
    }

    public function testDeleteLearnerThrowsExceptionWithStatusCodeWhenApiReturns404(): void
    {
        // Arrange
        $learner = $this->makeLearner($this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Learner not found'])));

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Learner not found');
        $this->expectExceptionCode(404);

        // Act
        $learner->deleteLearner(new AgoraId('per_missing'));
    }
}
