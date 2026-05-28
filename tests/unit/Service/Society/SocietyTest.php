<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\Society;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Input\Society\SocietyInput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Society\Society;
use AgoraLearningPhp\Tests\Service\ServiceTestCase;

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
        $dto = Society::convertDataToDTO($this->makeMinimalData());

        $this->assertInstanceOf(SocietyOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFields(): void
    {
        $dto = Society::convertDataToDTO($this->makeMinimalData());

        $this->assertSame('soc-uuid-001', $dto->uuid);
        $this->assertSame('Acme Corp', $dto->name);
    }

    public function testConvertDataToDTOSetsOptionalFieldsToNullWhenAbsent(): void
    {
        $dto = Society::convertDataToDTO($this->makeMinimalData());

        $this->assertNull($dto->siret);
        $this->assertNull($dto->legalPerson);
        $this->assertNull($dto->administrativePerson);
        $this->assertNull($dto->rhPerson);
    }

    public function testConvertDataToDTOMapsAllScalarFields(): void
    {
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

        $dto = Society::convertDataToDTO($data);

        $this->assertSame('12345678900001', $dto->siret);
        $this->assertSame('+33123456789', $dto->telephone);
        $this->assertSame('info@acme.com', $dto->email);
        $this->assertSame('SAS', $dto->legalStatus);
        $this->assertSame('1 rue Principale', $dto->addressStreetHeadOffice);
        $this->assertSame('75001', $dto->addressPostcodeHeadOffice);
        $this->assertSame('Paris', $dto->addressLocalityHeadOffice);
        $this->assertSame('FR', $dto->addressCountryHeadOffice);
        $this->assertSame('http://acme.com/logo.png', $dto->image);
    }

    public function testConvertDataToDTOWithLegalPerson(): void
    {
        $data = array_merge($this->makeMinimalData(), [
            'legalPerson' => $this->makePersonData(),
        ]);

        $dto = Society::convertDataToDTO($data);

        $this->assertInstanceOf(PersonOutput::class, $dto->legalPerson);
        $this->assertSame('person-uuid-001', $dto->legalPerson->uuid);
        $this->assertNull($dto->administrativePerson);
        $this->assertNull($dto->rhPerson);
    }

    public function testConvertDataToDTOWithAllPersons(): void
    {
        $data = array_merge($this->makeMinimalData(), [
            'legalPerson'          => array_merge($this->makePersonData(), ['id' => 'p1']),
            'administrativePerson' => array_merge($this->makePersonData(), ['id' => 'p2']),
            'rhPerson'             => array_merge($this->makePersonData(), ['id' => 'p3']),
        ]);

        $dto = Society::convertDataToDTO($data);

        $this->assertSame('p1', $dto->legalPerson->uuid);
        $this->assertSame('p2', $dto->administrativePerson->uuid);
        $this->assertSame('p3', $dto->rhPerson->uuid);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionSocietyReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        $society = $this->makeSociety($this->makeHttpClientWithResponse(200, '[]'));

        $result = $society->getCollectionSociety();

        $this->assertSame([], $result);
    }

    public function testGetSocietyThrowsExceptionWhenApiReturns404(): void
    {
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Society not found']))
        );

        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Society not found');

        $society->getSociety('missing-uuid');
    }

    public function testGetCollectionSocietyReturnsDTOArray(): void
    {
        $body    = json_encode([
            ['id' => 's1', 'name' => 'Corp A'],
            ['id' => 's2', 'name' => 'Corp B'],
        ]);
        $society = $this->makeSociety($this->makeHttpClientWithResponse(200, $body));

        $result = $society->getCollectionSociety();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(SocietyOutput::class, $result[0]);
        $this->assertSame('s1', $result[0]->uuid);
    }

    public function testGetSocietyReturnsSingleDTO(): void
    {
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(200, json_encode(['id' => 'soc-uuid-001', 'name' => 'Acme Corp']))
        );

        $result = $society->getSociety('soc-uuid-001');

        $this->assertInstanceOf(SocietyOutput::class, $result);
        $this->assertSame('soc-uuid-001', $result->uuid);
    }

    public function testPostSocietyWithPersonInputs(): void
    {
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(200, json_encode(['id' => 'soc-new-001', 'name' => 'NewCorp']))
        );
        $legalPerson = new PersonInput('Legal', 'Rep', Gender::GENDER_MALE);
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

        $result = $society->postSociety($input);

        $this->assertInstanceOf(SocietyOutput::class, $result);
    }

    public function testPostSocietyWithNullPersonInputs(): void
    {
        $society = $this->makeSociety(
            $this->makeHttpClientWithResponse(200, json_encode(['id' => 'soc-new-002', 'name' => 'EmptyCorp']))
        );

        $result = $society->postSociety(new SocietyInput('EmptyCorp'));

        $this->assertInstanceOf(SocietyOutput::class, $result);
    }
}
