<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\Learner;

use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Learner\Learner;
use AgoraLearningPhp\Tests\Service\ServiceTestCase;

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
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        $this->assertInstanceOf(LearnerOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        $this->assertSame('learner-uuid-001', $dto->id);
        $this->assertSame('jdupont', $dto->username);
        $this->assertSame('Dupont', $dto->familyName);
        $this->assertSame('Jean', $dto->givenName);
        $this->assertSame('jean@example.com', $dto->recoverEmail);
    }

    public function testConvertDataToDTOSetsGenderNaByDefault(): void
    {
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        $this->assertSame(Gender::GENDER_NA, $dto->gender);
    }

    public function testConvertDataToDTOSetsOptionalFieldsToNullWhenAbsent(): void
    {
        $dto = Learner::convertDataToDTO($this->makeMinimalData());

        $this->assertNull($dto->email);
        $this->assertNull($dto->telephone);
        $this->assertNull($dto->addressStreet);
        $this->assertNull($dto->addressPostcode);
        $this->assertNull($dto->addressLocality);
        $this->assertNull($dto->addressCountry);
        $this->assertNull($dto->image);
        $this->assertNull($dto->birthDate);
        $this->assertNull($dto->jobTitle);
    }

    public function testConvertDataToDTOMapsGender(): void
    {
        $data = array_merge($this->makeMinimalData(), ['gender' => Gender::GENDER_MALE]);

        $dto = Learner::convertDataToDTO($data);

        $this->assertSame(Gender::GENDER_MALE, $dto->gender);
    }

    public function testConvertDataToDTOParsesBirthDate(): void
    {
        $data = array_merge($this->makeMinimalData(), ['birthDate' => '1985-07-14T00:00:00+00:00']);

        $dto = Learner::convertDataToDTO($data);

        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->birthDate);
        $this->assertSame('1985-07-14', $dto->birthDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOReturnsNullBirthDateWhenFieldIsNull(): void
    {
        $data = array_merge($this->makeMinimalData(), ['birthDate' => null]);

        $dto = Learner::convertDataToDTO($data);

        $this->assertNull($dto->birthDate);
    }

    public function testConvertDataToDTOMapsAllOptionalFields(): void
    {
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

        $dto = Learner::convertDataToDTO($data);

        $this->assertSame('jean.work@example.com', $dto->email);
        $this->assertSame('+33611223344', $dto->telephone);
        $this->assertSame('1 rue Test', $dto->addressStreet);
        $this->assertSame('75001', $dto->addressPostcode);
        $this->assertSame('Paris', $dto->addressLocality);
        $this->assertSame('FR', $dto->addressCountry);
        $this->assertSame('http://img.example.com/jean.jpg', $dto->image);
        $this->assertSame('1985-07-14', $dto->birthDate->format('Y-m-d'));
        $this->assertSame('Ingénieur', $dto->jobTitle);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionLearnerReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        $learner = $this->makeLearner($this->makeHttpClientWithResponse(200, '[]'));

        $result = $learner->getCollectionLearner();

        $this->assertSame([], $result);
    }

    public function testGetCollectionLearnerReturnsDTOArray(): void
    {
        $body = json_encode([
            array_merge($this->makeMinimalData(), ['id' => 'l1', 'username' => 'user1']),
            array_merge($this->makeMinimalData(), ['id' => 'l2', 'username' => 'user2']),
        ]);
        $learner = $this->makeLearner($this->makeHttpClientWithResponse(200, $body));

        $result = $learner->getCollectionLearner();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(LearnerOutput::class, $result[0]);
        $this->assertSame('l1', $result[0]->id);
    }

    public function testGetLearnerReturnsSingleDTO(): void
    {
        $learner = $this->makeLearner(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalData()))
        );

        $result = $learner->getLearner('learner-uuid-001');

        $this->assertInstanceOf(LearnerOutput::class, $result);
        $this->assertSame('learner-uuid-001', $result->id);
    }

    public function testGetLearnerThrowsExceptionWhenApiReturns404(): void
    {
        $learner = $this->makeLearner(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Not Found']))
        );

        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Not Found');

        $learner->getLearner('missing-uuid');
    }

    public function testGetLearnerThrowsExceptionWithDefaultMessageWhenNoDetail(): void
    {
        $learner = $this->makeLearner(
            $this->makeHttpClientWithResponse(500, json_encode([]))
        );

        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('An unexpected error occured');

        $learner->getLearner('some-uuid');
    }

    public function testPostLearnerWithBirthDate(): void
    {
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'learner-new-001', 'username' => 'new_user']);
        $learner = $this->makeLearner(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );
        $input = new LearnerInput(
            'Nouveau',
            'Apprenant',
            'new@example.com',
            Gender::GENDER_NA,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            new \DateTimeImmutable('2000-01-01'),
            'Stagiaire'
        );

        $result = $learner->postLearner($input);

        $this->assertInstanceOf(LearnerOutput::class, $result);
        $this->assertSame('learner-new-001', $result->id);
    }

    public function testPostLearnerWithoutBirthDate(): void
    {
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'learner-new-002', 'username' => 'user2']);
        $learner = $this->makeLearner(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        $result = $learner->postLearner(new LearnerInput('Test', 'User', 'u@u.com'));

        $this->assertInstanceOf(LearnerOutput::class, $result);
    }
}
