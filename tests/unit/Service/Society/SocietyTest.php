<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Society;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
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
            'id'   => 'soc-uuid-001',
            'name' => 'Acme Corp',
        ];
    }

    private function makePersonData(): array
    {
        return [
            'id'         => 'person-uuid-001',
            'familyName' => 'Doe',
            'givenName'  => 'John',
            'email' => 'john.doe@example.net'
        ];
    }

    private function makeSociety(HttpClient $httpClient): Society
    {
        return new class($httpClient) extends Society {
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
            'legalPerson'          => array_merge($this->makePersonData(), ['id' => 'p1']),
            'administrativePerson' => array_merge($this->makePersonData(), ['id' => 'p2']),
            'rhPerson'             => array_merge($this->makePersonData(), ['id' => 'p3']),
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
            ['id' => 's1', 'name' => 'Corp A'],
            ['id' => 's2', 'name' => 'Corp B'],
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
            $this->makeHttpClientWithResponse(200, json_encode(['id' => 'soc-uuid-001', 'name' => 'Acme Corp']))
        );

        // Act
        $result = $society->getSociety('soc-uuid-001');

        // Assert
        $this->assertInstanceOf(SocietyOutput::class, $result);
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
        $society->getSociety('missing-uuid');
    }

    public function testPostSocietyWithPersonInputs(): void
    {
        // Arrange
        $society     = $this->makeSociety(
            $this->makeHttpClientWithResponse(200, json_encode(['id' => 'soc-new-001', 'name' => 'NewCorp']))
        );
        $legalPerson = new PersonInput('Legal', 'Rep', 'legal.rep@example.com', Gender::GENDER_MALE);
        $input       = new SocietyInput(
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
            $legalPerson
        );

        // Act
        $result = $society->postSociety($input);

        // Assert
        $this->assertInstanceOf(SocietyOutput::class, $result);
    }

    public function testPostSocietyWithNullPersonInputs(): void
    {
        // Arrange
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(200, json_encode(['id' => 'soc-new-002', 'name' => 'EmptyCorp']))
        );

        // Act
        $result = $society->postSociety(new SocietyInput('EmptyCorp'));

        // Assert
        $this->assertInstanceOf(SocietyOutput::class, $result);
    }

    public function testPostSocietySendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $society      = $this->makeSociety(
            $this->makeHttpClientInspectingBody(200, json_encode(['id' => 'soc-new-003', 'name' => 'FullCorp']), $capturedBody)
        );
        $input = new SocietyInput(
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
}
