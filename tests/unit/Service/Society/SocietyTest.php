<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Society;

use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyPatchInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Society\Society;
use AgoraLearningPhp\Tests\unit\Service\ServiceTestCase;

class SocietyTest extends ServiceTestCase
{
    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    private function makeMinimalData(): array
    {
        return [
            'agoraId' => 'soc-uuid-001',
            'name' => 'Acme Corp',
        ];
    }

    private function makePersonData(): array
    {
        return [
            'agoraId'    => 'person-uuid-001',
            'familyName' => 'Doe',
            'givenName'  => 'John',
            'email' => 'john.doe@example.net'
        ];
    }

    private function makeSociety(HttpClient $httpClient): Society
    {
        return new class ($httpClient) extends Society {
            public function __construct(HttpClient $client)
            {
                $this->httpClient = $client;
            }
        };
    }

    // -------------------------------------------------------------------------
    // convertDataToDTO — logique pure, sans réseau
    // -------------------------------------------------------------------------

    public function testConvertDataToDTOReturnsSocietyOutput(): void
    {
        // Act
        $dto = Society::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertInstanceOf(SocietyOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        // Act
        $dto = Society::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertSame('soc-uuid-001', $dto->uuid);
        $this->assertSame('Acme Corp', $dto->name);
    }

    public function testConvertDataToDTOMapsExternalIdentifier(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), [
            'externalIdentifier' => ['sourceName' => 'MonERP', 'id' => 'X-42'],
        ]);

        // Act
        $dto = Society::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(ExternalId::class, $dto->externalIdentifier);
        $this->assertSame('MonERP:X-42', $dto->externalIdentifier->getId());
    }

    public function testConvertDataToDTOSetsExternalIdentifierToNullWhenAbsent(): void
    {
        // Act
        $dto = Society::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertNull($dto->externalIdentifier);
    }

    public function testConvertDataToDTOSetsOptionalFieldsToNullWhenAbsent(): void
    {
        // Act
        $dto = Society::convertDataToDTO($this->makeMinimalData());

        // Assert
        $this->assertNull($dto->siret);
        $this->assertNull($dto->legalPerson);
        $this->assertNull($dto->administrativePerson);
        $this->assertNull($dto->rhPerson);
    }

    public function testConvertDataToDTOMapsAllScalarFields(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), [
            'siret'                        => '12345678900001',
            'telephone'                    => '+33123456789',
            'email'                        => 'info@acme.com',
            'legalStatus'                  => 'SAS',
            'addressStreetHeadOffice'      => '1 rue Principale',
            'addressPostcodeHeadOffice'    => '75001',
            'addressLocalityHeadOffice'    => 'Paris',
            'addressCountryHeadOffice'     => 'FR',
            'addressStreetInvoicing'       => '2 rue Facturation',
            'addressPostcodeInvoicing'     => '75002',
            'addressLocalityInvoicing'     => 'Paris',
            'addressCountryInvoicing'      => 'FR',
            'image'                        => 'http://acme.com/logo.png',
        ]);

        // Act
        $dto = Society::convertDataToDTO($data);

        // Assert
        $this->assertSame('12345678900001', $dto->siret);
        $this->assertSame('+33123456789', $dto->telephone);
        $this->assertSame('info@acme.com', $dto->email);
        $this->assertSame('SAS', $dto->legalStatus);
        $this->assertSame('1 rue Principale', $dto->addressStreetHeadOffice);
        $this->assertSame('75001', $dto->addressPostcodeHeadOffice);
        $this->assertSame('Paris', $dto->addressLocalityHeadOffice);
        $this->assertSame('FR', $dto->addressCountryHeadOffice);
    }

    public function testConvertDataToDTOWithLegalPerson(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), [
            'legalPerson' => $this->makePersonData(),
        ]);

        // Act
        $dto = Society::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(PersonOutput::class, $dto->legalPerson);
        $this->assertSame('person-uuid-001', $dto->legalPerson->uuid);
        $this->assertNull($dto->administrativePerson);
        $this->assertNull($dto->rhPerson);
    }

    public function testConvertDataToDTOWithAllPersons(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalData(), [
            'legalPerson'          => array_merge($this->makePersonData(), ['agoraId' => 'p1']),
            'administrativePerson' => array_merge($this->makePersonData(), ['agoraId' => 'p2']),
            'rhPerson'             => array_merge($this->makePersonData(), ['agoraId' => 'p3']),
        ]);

        // Act
        $dto = Society::convertDataToDTO($data);

        // Assert
        $this->assertSame('p1', $dto->legalPerson->uuid);
        $this->assertSame('p2', $dto->administrativePerson->uuid);
        $this->assertSame('p3', $dto->rhPerson->uuid);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionSocietyReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        // Arrange
        $society = $this->makeSociety($this->makeHttpClientWithResponse(200, '[]'));

        // Act
        $result = $society->getCollectionSociety();

        // Assert
        $this->assertSame([], $result);
    }

    public function testGetCollectionSocietyReturnsDTOArray(): void
    {
        // Arrange
        $body    = json_encode([
            ['agoraId' => 's1', 'name' => 'Corp A'],
            ['agoraId' => 's2', 'name' => 'Corp B'],
        ]);
        $society = $this->makeSociety($this->makeHttpClientWithResponse(200, $body));

        // Act
        $result = $society->getCollectionSociety();

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(SocietyOutput::class, $result[0]);
        $this->assertSame('s1', $result[0]->uuid);
    }

    public function testGetSocietyReturnsSingleDTO(): void
    {
        // Arrange
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(200, json_encode(['agoraId' => 'soc-uuid-001', 'name' => 'Acme Corp']))
        );

        // Act
        $result = $society->getSociety(new AgoraId('soc-uuid-001'));

        // Assert
        $this->assertInstanceOf(SocietyOutput::class, $result);
        $this->assertSame('soc-uuid-001', $result->uuid);
    }

    public function testGetSocietyWithExternalIdCallsEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $society = $this->makeSociety($this->makeHttpClientInspectingRequest(
            200,
            json_encode(['agoraId' => 'soc-uuid-001', 'name' => 'Acme Corp']),
            $method,
            $url
        ));

        // Act
        $result = $society->getSociety(new ExternalId('Src', 'X-42'));

        // Assert
        $this->assertSame('GET', $method);
        $this->assertSame('https://api.test.local/api/external/v1/societies/Src%3AX-42', $url);
        $this->assertSame('soc-uuid-001', $result->uuid);
    }

    public function testGetSocietyThrowsExceptionWhenApiReturns404(): void
    {
        // Arrange
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Society not found']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Society not found');

        // Act
        $society->getSociety(new AgoraId('missing-uuid'));
    }

    public function testPostSocietySendsPersonIdentifiers(): void
    {
        // Arrange
        $capturedBody = '';
        $society = $this->makeSociety(
            $this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'soc_new_001', 'sourceName' => 'Test', 'externalId' => 'SOC-1']), $capturedBody)
        );
        $input = new SocietyInput(
            new ExternalId('Test', 'SOC-1'),
            'NewCorp',
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            'Test:P-1',
            'per_admin',
            null
        );

        // Act
        $result = $society->postSociety($input);

        // Assert
        $this->assertInstanceOf(CreatedOutput::class, $result);
        $body = json_decode($capturedBody, true);
        $this->assertSame('Test:P-1', $body['legalPersonId']);
        $this->assertSame('per_admin', $body['administrativePersonId']);
        $this->assertArrayHasKey('rhPersonId', $body);
        $this->assertNull($body['rhPersonId']);
        $this->assertArrayNotHasKey('legalPerson', $body);
    }

    public function testPostSocietyWithNullPersonInputs(): void
    {
        // Arrange
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(200, json_encode(['agoraId' => 'soc_new_002', 'sourceName' => 'Test', 'externalId' => 'SOC-2']))
        );

        // Act
        $result = $society->postSociety(new SocietyInput(new ExternalId('Test', 'SOC-2'), 'EmptyCorp'));

        // Assert
        $this->assertInstanceOf(CreatedOutput::class, $result);
    }

    public function testPostSocietySendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $society      = $this->makeSociety(
            $this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'soc_new_003', 'sourceName' => 'Test', 'externalId' => 'SOC-3']), $capturedBody)
        );
        $input = new SocietyInput(
            new ExternalId('Test', 'SOC-3'),
            'FullCorp',
            '12345678900001',
            'SAS',
            'info@fullcorp.com',
            '+33123456789',
            '1 rue Principale',
            '75001',
            'Paris',
            'FR',
            '2 rue Facturation',
            '75002',
            'Paris',
            'FR',
            null,
            null,
            null,
            'http://fullcorp.com/logo.png'
        );

        // Act
        $society->postSociety($input);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame('FullCorp', $body['name']);
        $this->assertSame('12345678900001', $body['siret']);
        $this->assertSame('SAS', $body['legalStatus']);
        $this->assertSame('info@fullcorp.com', $body['email']);
        $this->assertSame('+33123456789', $body['telephone']);
        $this->assertSame('1 rue Principale', $body['addressStreetHeadOffice']);
        $this->assertSame('75001', $body['addressPostcodeHeadOffice']);
        $this->assertSame('Paris', $body['addressLocalityHeadOffice']);
        $this->assertSame('FR', $body['addressCountryHeadOffice']);
        $this->assertSame('2 rue Facturation', $body['addressStreetInvoicing']);
        $this->assertSame('75002', $body['addressPostcodeInvoicing']);
        $this->assertSame('Paris', $body['addressLocalityInvoicing']);
        $this->assertSame('FR', $body['addressCountryInvoicing']);
        $this->assertSame('http://fullcorp.com/logo.png', $body['image']);
    }

    public function testGetCollectionSocietyThrowsExceptionWithDetailOnError(): void
    {
        // Arrange
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Collection not found']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Collection not found');

        // Act
        $society->getCollectionSociety();
    }

    public function testGetCollectionSocietyThrowsDefaultMessageWithoutDetail(): void
    {
        // Arrange
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(500, json_encode([]))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('An unexpected error occured');

        // Act
        $society->getCollectionSociety();
    }

    // -------------------------------------------------------------------------
    // PUT / PATCH
    // -------------------------------------------------------------------------

    public function testPutSocietySendsPutRequestToEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $society = $this->makeSociety($this->makeHttpClientInspectingRequest(
            200,
            json_encode(['agoraId' => 'soc_001', 'sourceName' => 'Src', 'externalId' => 'X-42']),
            $method,
            $url
        ));

        // Act
        $result = $society->putSociety(new ExternalId('Src', 'X-42'), new SocietyInput(new ExternalId('Src', 'X-42'), 'Corp'));

        // Assert
        $this->assertSame('PUT', $method);
        $this->assertSame('https://api.test.local/api/external/v1/societies/Src%3AX-42', $url);
        $this->assertSame('soc_001', $result->agoraId->getId());
    }

    public function testPutSocietySendsFullBodyWithExternalIdKey(): void
    {
        // Arrange
        $capturedBody = '';
        $society = $this->makeSociety(
            $this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'soc_001']), $capturedBody)
        );

        // Act
        $society->putSociety(new AgoraId('soc_001'), new SocietyInput(new ExternalId('Src', 'X-42'), 'Corp'));

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame(['sourceName' => 'Src', 'id' => 'X-42'], $body['externalId']);
        $this->assertSame('Corp', $body['name']);
        $this->assertArrayHasKey('siret', $body);
        $this->assertNull($body['siret']);
        $this->assertArrayHasKey('legalPersonId', $body);
    }

    public function testPatchSocietySendsOnlySetFields(): void
    {
        // Arrange
        $capturedBody = '';
        $society = $this->makeSociety(
            $this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'soc_001']), $capturedBody)
        );
        $patch = (new SocietyPatchInput())
            ->setTelephone('0102030405')
            ->setRhPersonId(null)
            ->setExternalIdentifier(new ExternalId('Src', 'X-43'));

        // Act
        $society->patchSociety(new AgoraId('soc_001'), $patch);

        // Assert
        $this->assertSame(
            ['telephone' => '0102030405', 'rhPersonId' => null, 'externalId' => ['sourceName' => 'Src', 'id' => 'X-43']],
            json_decode($capturedBody, true)
        );
    }

    public function testPatchSocietySendsPatchRequest(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $society = $this->makeSociety($this->makeHttpClientInspectingRequest(
            200,
            json_encode(['agoraId' => 'soc_001']),
            $method,
            $url
        ));

        // Act
        $society->patchSociety(new AgoraId('soc_001'), (new SocietyPatchInput())->setName('Corp'));

        // Assert
        $this->assertSame('PATCH', $method);
        $this->assertSame('https://api.test.local/api/external/v1/societies/soc_001', $url);
    }

    public function testPatchSocietyThrowsExceptionWhenApiReturns422(): void
    {
        // Arrange
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(422, json_encode(['detail' => 'name: Obligatoire']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('name: Obligatoire');

        // Act
        $society->patchSociety(new AgoraId('soc_001'), new SocietyPatchInput());
    }

    // -------------------------------------------------------------------------
    // deleteSociety
    // -------------------------------------------------------------------------

    public function testDeleteSocietyWithExternalIdSendsDeleteToEncodedUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $society = $this->makeSociety($this->makeHttpClientInspectingRequest(204, '', $method, $url));

        // Act
        $society->deleteSociety(new ExternalId('Src', 'X-42'));

        // Assert
        $this->assertSame('DELETE', $method);
        $this->assertSame('https://api.test.local/api/external/v1/societies/Src%3AX-42', $url);
    }

    public function testDeleteSocietyThrowsExceptionWithStatusCodeWhenApiReturns404(): void
    {
        // Arrange
        $society = $this->makeSociety($this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Society not found'])));

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Society not found');
        $this->expectExceptionCode(404);

        // Act
        $society->deleteSociety(new AgoraId('soc_missing'));
    }

    // -------------------------------------------------------------------------
    // detachSociety
    // -------------------------------------------------------------------------

    public function testDetachSocietyWithExternalIdSendsPatchToEncodedSocietyUrl(): void
    {
        // Arrange
        $method = '';
        $url = '';
        $society = $this->makeSociety($this->makeHttpClientInspectingRequest(
            200,
            json_encode(['agoraId' => 'soc_001']),
            $method,
            $url
        ));

        // Act
        $society->detachSociety(new ExternalId('Src', 'X-42'));

        // Assert
        $this->assertSame('PATCH', $method);
        $this->assertSame('https://api.test.local/api/external/v1/societies/Src%3AX-42', $url);
    }

    public function testDetachSocietySendsOnlyANullExternalId(): void
    {
        // Arrange
        $capturedBody = '';
        $society = $this->makeSociety(
            $this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'soc_001']), $capturedBody)
        );

        // Act
        $society->detachSociety(new AgoraId('soc_001'));

        // Assert
        $this->assertSame(['externalId' => null], json_decode($capturedBody, true));
    }

    public function testPatchSocietyCanClearExternalIdentifier(): void
    {
        // Arrange
        $capturedBody = '';
        $society = $this->makeSociety(
            $this->makeHttpClientInspectingBody(200, json_encode(['agoraId' => 'soc_001']), $capturedBody)
        );

        // Act
        $society->patchSociety(new AgoraId('soc_001'), (new SocietyPatchInput())->clearExternalIdentifier());

        // Assert
        $this->assertSame(['externalId' => null], json_decode($capturedBody, true));
    }

    public function testDetachSocietyThrowsExceptionWithStatusCodeWhenApiReturns404(): void
    {
        // Arrange
        $society = $this->makeSociety($this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Society not found'])));

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Society not found');
        $this->expectExceptionCode(404);

        // Act
        $society->detachSociety(new AgoraId('soc_missing'));
    }

    public function testGetSocietyThrowsExceptionWithStatusCodeWhenApiReturns404(): void
    {
        // Arrange
        $society = $this->makeSociety($this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Society not found'])));

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Society not found');
        $this->expectExceptionCode(404);

        // Act
        $society->getSociety(new AgoraId('soc_missing'));
    }
}
