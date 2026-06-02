<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Person;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Person\Person;
use AgoraLearningPhp\Tests\unit\Service\ServiceTestCase;

class PersonTest extends ServiceTestCase
{
    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    private function makeMinimalData(): array
    {
        return [
            'id'         => 'person-uuid-001',
            'familyName' => 'Martin',
            'givenName'  => 'Claire',
            'email'  => 'claire.martin@example.net'
        ];
    }

    private function makePerson(HttpClient $httpClient): Person
    {
        return new class($httpClient) extends Person {
            public function __construct(HttpClient $client)
            {
                $this->httpClient = $client;
            }
        };
    }

    // -------------------------------------------------------------------------
    // convertDataToDTO — logique pure, sans réseau
    // -------------------------------------------------------------------------

    public function testConvertDataToDTOReturnsPersonOutput(): void
    {
        // Act
        $dto = Person::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertInstanceOf(PersonOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        // Act
        $dto = Person::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertSame('person-uuid-001', $dto->uuid);
        $this->assertSame('Martin', $dto->familyName);
        $this->assertSame('Claire', $dto->givenName);
    }

    public function testConvertDataToDTOSetsGenderNaByDefault(): void
    {
        // Act
        $dto = Person::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertSame(Gender::GENDER_NA, $dto->gender);
    }

    public function testConvertDataToDTOSetsOptionalFieldsToNullWhenAbsent(): void
    {
        // Act
        $dto = Person::convertDataToDTO($this->makeMinimalData());

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
        $data = array_merge($this->makeMinimalData(), ['gender' => Gender::GENDER_FEMALE]);

        // Act
        $dto = Person::convertDataToDTO($data);

        // Assert
        $this->assertSame(Gender::GENDER_FEMALE, $dto->gender);
    }

    public function testConvertDataToDTOParsesBirthDate(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), ['birthDate' => '1990-05-20T00:00:00+00:00']);

        // Act
        $dto = Person::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->birthDate);
        $this->assertSame('1990-05-20', $dto->birthDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOReturnsNullBirthDateWhenFieldIsNull(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), ['birthDate' => null]);

        // Act
        $dto = Person::convertDataToDTO($data);

        // Assert
        $this->assertNull($dto->birthDate);
    }

    public function testConvertDataToDTOMapsAllOptionalFields(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), [
            'gender'          => Gender::GENDER_FEMALE,
            'email'           => 'claire@example.com',
            'telephone'       => '+33699001122',
            'addressStreet'   => '2 bis rue des Acacias',
            'addressPostcode' => '06000',
            'addressLocality' => 'Nice',
            'addressCountry'  => 'FR',
            'image'           => 'http://img.example.com/claire.jpg',
            'birthDate'       => '1990-05-20T00:00:00+00:00',
            'jobTitle'        => 'Chef de projet',
        ]);

        // Act
        $dto = Person::convertDataToDTO($data);

        // Assert
        $this->assertSame('claire@example.com', $dto->email);
        $this->assertSame('+33699001122', $dto->telephone);
        $this->assertSame('2 bis rue des Acacias', $dto->addressStreet);
        $this->assertSame('06000', $dto->addressPostcode);
        $this->assertSame('Nice', $dto->addressLocality);
        $this->assertSame('FR', $dto->addressCountry);
        $this->assertSame('1990-05-20', $dto->birthDate->format('Y-m-d'));
        $this->assertSame('Chef de projet', $dto->jobTitle);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionPersonReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        // Arrange
        $person = $this->makePerson($this->makeHttpClientWithResponse(200, '[]'));

        // Act
        $result = $person->getCollectionPerson();

        // Assert
        $this->assertSame([], $result);
    }

    public function testGetCollectionPersonReturnsDTOArray(): void
    {
        // Arrange
        $body   = json_encode([
            array_merge($this->makeMinimalData(), ['id' => 'p1']),
            array_merge($this->makeMinimalData(), ['id' => 'p2']),
        ]);
        $person = $this->makePerson($this->makeHttpClientWithResponse(200, $body));

        // Act
        $result = $person->getCollectionPerson();

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(PersonOutput::class, $result[0]);
        $this->assertSame('p1', $result[0]->uuid);
    }

    public function testGetPersonReturnsSingleDTO(): void
    {
        // Arrange
        $person = $this->makePerson(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalData()))
        );

        // Act
        $result = $person->getPerson('person-uuid-001');

        // Assert
        $this->assertInstanceOf(PersonOutput::class, $result);
        $this->assertSame('person-uuid-001', $result->uuid);
    }

    public function testGetPersonThrowsExceptionWhenApiReturns404(): void
    {
        // Arrange
        $person = $this->makePerson(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Person not found']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Person not found');

        // Act
        $person->getPerson('missing-uuid');
    }

    public function testPostPersonWithBirthDate(): void
    {
        // Arrange
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'person-new-001']);
        $person       = $this->makePerson(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );
        $input = new PersonInput(
            'Nouveau',
            'Contact',
            'contact@example.com',
            Gender::GENDER_MALE,
            null,
            null,
            null,
            null,
            null,
            null,
            new \DateTimeImmutable('1990-06-15'),
            'Directeur'
        );

        // Act
        $result = $person->postPerson($input);

        // Assert
        $this->assertInstanceOf(PersonOutput::class, $result);
        $this->assertSame('person-new-001', $result->uuid);
    }

    public function testPostPersonWithoutBirthDate(): void
    {
        // Arrange
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'person-new-002']);
        $person       = $this->makePerson(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        // Act
        $result = $person->postPerson(new PersonInput('Test', 'Contact', 'contact@example.com'));

        // Assert
        $this->assertInstanceOf(PersonOutput::class, $result);
    }

    public function testPostPersonSendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'person-new-003']);
        $person       = $this->makePerson(
            $this->makeHttpClientInspectingBody(200, json_encode($responseData), $capturedBody)
        );
        $input = new PersonInput(
            'Martin',
            'Claire',
            'claire@example.com',
            Gender::GENDER_FEMALE,
            '+33699001122',
            '2 rue des Acacias',
            '06000',
            'Nice',
            'FR',
            'http://img.example.com/claire.jpg',
            new \DateTimeImmutable('1990-05-20'),
            'Chef de projet'
        );

        // Act
        $person->postPerson($input);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame('Martin', $body['familyName']);
        $this->assertSame('Claire', $body['givenName']);
        $this->assertSame(Gender::GENDER_FEMALE, $body['gender']);
        $this->assertArrayHasKey('birthDate', $body);
        $this->assertSame('Chef de projet', $body['jobTitle']);
        $this->assertSame('+33699001122', $body['telephone']);
        $this->assertSame('claire@example.com', $body['email']);
        $this->assertSame('2 rue des Acacias', $body['addressStreet']);
        $this->assertSame('06000', $body['addressPostcode']);
        $this->assertSame('Nice', $body['addressLocality']);
        $this->assertSame('FR', $body['addressCountry']);
        $this->assertSame('http://img.example.com/claire.jpg', $body['image']);
    }
}
