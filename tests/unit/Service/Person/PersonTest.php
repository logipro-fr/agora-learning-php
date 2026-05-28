<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\Person;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Person\Person;
use AgoraLearningPhp\Tests\Service\ServiceTestCase;

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
        $dto = Person::convertDataToDTO($this->makeMinimalData());

        $this->assertInstanceOf(PersonOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        $dto = Person::convertDataToDTO($this->makeMinimalData());

        $this->assertSame('person-uuid-001', $dto->uuid);
        $this->assertSame('Martin', $dto->familyName);
        $this->assertSame('Claire', $dto->givenName);
    }

    public function testConvertDataToDTOSetsGenderNaByDefault(): void
    {
        $dto = Person::convertDataToDTO($this->makeMinimalData());

        $this->assertSame(Gender::GENDER_NA, $dto->gender);
    }

    public function testConvertDataToDTOSetsOptionalFieldsToNullWhenAbsent(): void
    {
        $dto = Person::convertDataToDTO($this->makeMinimalData());

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
        $data = array_merge($this->makeMinimalData(), ['gender' => Gender::GENDER_FEMALE]);

        $dto = Person::convertDataToDTO($data);

        $this->assertSame(Gender::GENDER_FEMALE, $dto->gender);
    }

    public function testConvertDataToDTOParsesBirthDate(): void
    {
        $data = array_merge($this->makeMinimalData(), ['birthDate' => '1990-05-20T00:00:00+00:00']);

        $dto = Person::convertDataToDTO($data);

        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->birthDate);
        $this->assertSame('1990-05-20', $dto->birthDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOReturnsNullBirthDateWhenFieldIsNull(): void
    {
        $data = array_merge($this->makeMinimalData(), ['birthDate' => null]);

        $dto = Person::convertDataToDTO($data);

        $this->assertNull($dto->birthDate);
    }

    public function testConvertDataToDTOMapsAllOptionalFields(): void
    {
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

        $dto = Person::convertDataToDTO($data);

        $this->assertSame('claire@example.com', $dto->email);
        $this->assertSame('+33699001122', $dto->telephone);
        $this->assertSame('2 bis rue des Acacias', $dto->addressStreet);
        $this->assertSame('06000', $dto->addressPostcode);
        $this->assertSame('Nice', $dto->addressLocality);
        $this->assertSame('FR', $dto->addressCountry);
        $this->assertSame('http://img.example.com/claire.jpg', $dto->image);
        $this->assertSame('1990-05-20', $dto->birthDate->format('Y-m-d'));
        $this->assertSame('Chef de projet', $dto->jobTitle);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionPersonReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        $person = $this->makePerson($this->makeHttpClientWithResponse(200, '[]'));

        $result = $person->getCollectionPerson();

        $this->assertSame([], $result);
    }

    public function testGetCollectionPersonReturnsDTOArray(): void
    {
        $body = json_encode([
            array_merge($this->makeMinimalData(), ['id' => 'p1']),
            array_merge($this->makeMinimalData(), ['id' => 'p2']),
        ]);
        $person = $this->makePerson($this->makeHttpClientWithResponse(200, $body));

        $result = $person->getCollectionPerson();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(PersonOutput::class, $result[0]);
        $this->assertSame('p1', $result[0]->uuid);
    }

    public function testGetPersonReturnsSingleDTO(): void
    {
        $person = $this->makePerson(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalData()))
        );

        $result = $person->getPerson('person-uuid-001');

        $this->assertInstanceOf(PersonOutput::class, $result);
        $this->assertSame('person-uuid-001', $result->uuid);
    }

    public function testGetPersonThrowsExceptionWhenApiReturns404(): void
    {
        $person = $this->makePerson(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Person not found']))
        );

        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Person not found');

        $person->getPerson('missing-uuid');
    }

    public function testPostPersonWithBirthDate(): void
    {
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'person-new-001']);
        $person = $this->makePerson(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );
        $input = new PersonInput(
            'Nouveau',
            'Contact',
            Gender::GENDER_MALE,
            'contact@example.com',
            null,
            null,
            null,
            null,
            null,
            null,
            new \DateTimeImmutable('1990-06-15'),
            'Directeur'
        );

        $result = $person->postPerson($input);

        $this->assertInstanceOf(PersonOutput::class, $result);
        $this->assertSame('person-new-001', $result->uuid);
    }

    public function testPostPersonWithoutBirthDate(): void
    {
        $responseData = array_merge($this->makeMinimalData(), ['id' => 'person-new-002']);
        $person = $this->makePerson(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        $result = $person->postPerson(new PersonInput('Test', 'Contact'));

        $this->assertInstanceOf(PersonOutput::class, $result);
    }
}
