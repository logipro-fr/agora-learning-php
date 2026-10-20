<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Person;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Person\PersonPatchInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;
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
            'agoraId'    => 'person-uuid-001',
            'familyName' => 'Martin',
            'givenName'  => 'Claire',
            'email'  => 'claire.martin@example.net'
        ];
    }

    private function makeCreatedData(string $agoraId): array
    {
        return [
            'agoraId'    => $agoraId,
            'sourceName' => 'Test',
            'externalId' => 'P-1',
        ];
    }

    private function makePerson(HttpClient $httpClient): Person
    {
        return new class ($httpClient) extends Person {
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

    public function testConvertDataToDTOMapsSocietyId(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), ['societyId' => 'soc-uuid-001']);

        // Act
        $dto = Person::convertDataToDTO($data);

        // Assert
        $this->assertSame('soc-uuid-001', $dto->societyId);
    }

    public function testConvertDataToDTOSetsSocietyIdToNullWhenAbsent(): void
    {
        // Act
        $dto = Person::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertNull($dto->societyId);
    }

    public function testConvertDataToDTOMapsExternalIdentifier(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), [
            'externalIdentifier' => ['sourceName' => 'MonERP', 'id' => 'P-42'],
        ]);

        // Act
        $dto = Person::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(ExternalId::class, $dto->externalIdentifier);
        $this->assertSame('MonERP:P-42', $dto->externalIdentifier->getId());
    }

    public function testConvertDataToDTOSetsExternalIdentifierToNullWhenAbsent(): void
    {
        // Act
        $dto = Person::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertNull($dto->externalIdentifier);
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
            array_merge($this->makeMinimalData(), ['agoraId' => 'p1']),
            array_merge($this->makeMinimalData(), ['agoraId' => 'p2']),
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
        $result = $person->getPerson(new AgoraId('person-uuid-001'));

        // Assert
        $this->assertInstanceOf(PersonOutput::class, $result);
        $this->assertSame('person-uuid-001', $result->uuid);
    }

    public function testGetPersonWithExternalIdCallsEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $person = $this->makePerson($this->makeHttpClientInspectingRequest(
            200,
            json_encode($this->makeMinimalData()),
            $method,
            $url
        ));

        // Act
        $result = $person->getPerson(new ExternalId('Src', 'X-42'));

        // Assert
        $this->assertSame('GET', $method);
        $this->assertSame('https://api.test.local/api/external/v1/persons/Src%3AX-42', $url);
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
        $person->getPerson(new AgoraId('missing-uuid'));
    }

    public function testPostPersonWithBirthDate(): void
    {
        // Arrange
        $responseData = $this->makeCreatedData('per_new_001');
        $person       = $this->makePerson(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );
        $input = new PersonInput(
            new ExternalId('Test', 'P-1'),
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
        $this->assertInstanceOf(CreatedOutput::class, $result);
        $this->assertSame('per_new_001', $result->agoraId->getId());
        $this->assertNotNull($result->externalId);
        $this->assertSame('Test:P-1', $result->externalId->getId());
    }

    public function testPostPersonWithoutBirthDate(): void
    {
        // Arrange
        $responseData = $this->makeCreatedData('per_new_002');
        $person       = $this->makePerson(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        // Act
        $result = $person->postPerson(new PersonInput(new ExternalId('Test', 'P-2'), 'Test', 'Contact', 'contact@example.com'));

        // Assert
        $this->assertInstanceOf(CreatedOutput::class, $result);
    }

    public function testPostPersonSendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $responseData = $this->makeCreatedData('per_new_003');
        $person       = $this->makePerson(
            $this->makeHttpClientInspectingBody(200, json_encode($responseData), $capturedBody)
        );
        $input = new PersonInput(
            new ExternalId('Test', 'P-3'),
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

    // -------------------------------------------------------------------------
    // PUT / PATCH
    // -------------------------------------------------------------------------

    public function testPostPersonSendsSocietyId(): void
    {
        // Arrange
        $capturedBody = '';
        $person = $this->makePerson($this->makeHttpClientInspectingBody(200, json_encode($this->makeCreatedData('per_soc')), $capturedBody));
        $input = new PersonInput(
            new ExternalId('Src', 'P-1'),
            'Dupont',
            'Jean',
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
            'Src:S-1'
        );

        // Act
        $person->postPerson($input);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame('Src:S-1', $body['societyId']);
    }

    public function testPostPersonSendsNullSocietyIdByDefault(): void
    {
        // Arrange
        $capturedBody = '';
        $person = $this->makePerson($this->makeHttpClientInspectingBody(200, json_encode($this->makeCreatedData('per_soc')), $capturedBody));

        // Act
        $person->postPerson(new PersonInput(new ExternalId('Src', 'P-2'), 'Dupont', 'Jean', 'jean@example.com'));

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertArrayHasKey('societyId', $body);
        $this->assertNull($body['societyId']);
    }

    public function testPatchPersonSendsSocietyIdNullToRemoveRelation(): void
    {
        // Arrange
        $capturedBody = '';
        $person = $this->makePerson($this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $capturedBody));

        // Act
        $person->patchPerson(new AgoraId('per_001'), (new PersonPatchInput())->setSocietyId(null));

        // Assert
        $this->assertSame(['societyId' => null], json_decode($capturedBody, true));
    }

    public function testPutPersonSendsPutRequestToEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $person = $this->makePerson($this->makeHttpClientInspectingRequest(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $method, $url));

        // Act
        $result = $person->putPerson(
            new ExternalId('Src', 'X-42'),
            new PersonInput(new ExternalId('Src', 'X-42'), 'Martin', 'Claire', 'claire@example.net')
        );

        // Assert
        $this->assertSame('PUT', $method);
        $this->assertSame('https://api.test.local/api/external/v1/persons/Src%3AX-42', $url);
        $this->assertInstanceOf(CreatedOutput::class, $result);
        $this->assertSame('per_001', $result->agoraId->getId());
        $this->assertSame('Src:X-42', $result->externalId->getId());
    }

    public function testPutPersonSendsAllFieldsIncludingNullOptionals(): void
    {
        // Arrange
        $capturedBody = '';
        $person = $this->makePerson($this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $capturedBody));
        $input = new PersonInput(
            new ExternalId('Src', 'X-42'),
            'Martin',
            'Claire',
            'claire@example.net',
            Gender::GENDER_FEMALE,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            '0601020304'
        );

        // Act
        $person->putPerson(new AgoraId('per_001'), $input);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame(['sourceName' => 'Src', 'id' => 'X-42'], $body['externalIdentifier']);
        $this->assertSame('Martin', $body['familyName']);
        $this->assertSame(Gender::GENDER_FEMALE, $body['gender']);
        $this->assertSame('0601020304', $body['mobileNumber']);
        $this->assertArrayHasKey('telephone', $body);
        $this->assertNull($body['telephone']);
    }

    public function testPatchPersonSendsPatchRequestWithOnlySetFields(): void
    {
        // Arrange
        $capturedBody = '';
        $person = $this->makePerson($this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $capturedBody));
        $patch = (new PersonPatchInput())
            ->setTelephone('0102030405')
            ->setJobTitle(null)
            ->setBirthDate(new \DateTimeImmutable('1990-05-20T00:00:00+00:00'));

        // Act
        $result = $person->patchPerson(new AgoraId('per_001'), $patch);

        // Assert
        $this->assertSame(
            ['telephone' => '0102030405', 'jobTitle' => null, 'birthDate' => '1990-05-20T00:00:00+00:00'],
            json_decode($capturedBody, true)
        );
        $this->assertSame('per_001', $result->agoraId->getId());
    }

    public function testPatchPersonSendsPatchMethodToPersonUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $person = $this->makePerson($this->makeHttpClientInspectingRequest(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $method, $url));

        // Act
        $person->patchPerson(new AgoraId('per_001'), (new PersonPatchInput())->setFamilyName('Dupont'));

        // Assert
        $this->assertSame('PATCH', $method);
        $this->assertSame('https://api.test.local/api/external/v1/persons/per_001', $url);
    }

    public function testPatchPersonWithEmptyPatchSendsEmptyJsonObject(): void
    {
        // Arrange
        $capturedBody = '';
        $person = $this->makePerson($this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'per_001', 'sourceName' => 'Src', 'externalId' => 'X-42']), $capturedBody));

        // Act
        $person->patchPerson(new AgoraId('per_001'), new PersonPatchInput());

        // Assert
        $this->assertSame('{}', $capturedBody);
    }

    public function testPatchPersonThrowsExceptionWhenApiReturns404(): void
    {
        // Arrange
        $person = $this->makePerson($this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Person not found'])));

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Person not found');
        $this->expectExceptionCode(404);

        // Act
        $person->patchPerson(new AgoraId('per_missing'), (new PersonPatchInput())->setFamilyName('Dupont'));
    }

    // -------------------------------------------------------------------------
    // deletePerson
    // -------------------------------------------------------------------------

    public function testDeletePersonWithExternalIdSendsDeleteToEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $person = $this->makePerson($this->makeHttpClientInspectingRequest(204, '', $method, $url));

        // Act
        $person->deletePerson(new ExternalId('Src', 'X-42'));

        // Assert
        $this->assertSame('DELETE', $method);
        $this->assertSame('https://api.test.local/api/external/v1/persons/Src%3AX-42', $url);
    }

    public function testDeletePersonThrowsExceptionWithStatusCodeWhenApiReturns404(): void
    {
        // Arrange
        $person = $this->makePerson($this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Person not found'])));

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Person not found');
        $this->expectExceptionCode(404);

        // Act
        $person->deletePerson(new AgoraId('per_missing'));
    }
}
