<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\Trainer;

use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerEmployeeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerFreeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Enum\TrainerStatut;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\Trainer\Trainer;
use AgoraLearningPhp\Tests\Service\ServiceTestCase;

class TrainerTest extends ServiceTestCase
{
    // -------------------------------------------------------------------------
    // Fixtures
    // -------------------------------------------------------------------------

    private function makeMinimalEmployeeData(): array
    {
        return [
            'id'                        => 'trainer-emp-001',
            'familyName'                => 'Dupont',
            'givenName'                 => 'Jean',
            'email'                     => 'jean@example.com',
            'visibleInformation'        => true,
            'gender'                    => Gender::GENDER_NA,
            'educationalManagerSessions' => [],
            'statut'                    => TrainerStatut::STATUT_EMPLOYE,
        ];
    }

    private function makeMinimalFreeData(): array
    {
        return [
            'id'                        => 'trainer-free-001',
            'familyName'                => 'Leclerc',
            'givenName'                 => 'Paul',
            'email'                     => 'paul@example.com',
            'visibleInformation'        => true,
            'gender'                    => Gender::GENDER_NA,
            'educationalManagerSessions' => [],
            'statut'                    => TrainerStatut::STATUT_FREE,
        ];
    }

    private function makeTrainer(HttpClient $httpClient): Trainer
    {
        return new class($httpClient) extends Trainer {
            public function __construct(HttpClient $client)
            {
                $this->httpClient = $client;
            }
        };
    }

    // -------------------------------------------------------------------------
    // convertDataToDTO — logique pure, sans réseau
    // -------------------------------------------------------------------------

    public function testConvertDataToDTOReturnsTrainerEmployeeOutputWhenStatutIsEmployee(): void
    {
        $dto = Trainer::convertDataToDTO($this->makeMinimalEmployeeData());

        $this->assertInstanceOf(TrainerEmployeeOutput::class, $dto);
    }

    public function testConvertDataToDTOReturnsTrainerFreeOutputWhenStatutIsFree(): void
    {
        $dto = Trainer::convertDataToDTO($this->makeMinimalFreeData());

        $this->assertInstanceOf(TrainerFreeOutput::class, $dto);
    }

    public function testConvertDataToDTODefaultsToEmployeeWhenStatutIsMissing(): void
    {
        $data = $this->makeMinimalEmployeeData();
        unset($data['statut']);

        $dto = Trainer::convertDataToDTO($data);

        $this->assertInstanceOf(TrainerEmployeeOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFieldsForEmployee(): void
    {
        $dto = Trainer::convertDataToDTO($this->makeMinimalEmployeeData());

        $this->assertSame('trainer-emp-001', $dto->uuid);
        $this->assertSame('Dupont', $dto->familyName);
        $this->assertSame('Jean', $dto->givenName);
        $this->assertSame('jean@example.com', $dto->email);
        $this->assertTrue($dto->visibleInformation);
        $this->assertSame(Gender::GENDER_NA, $dto->gender);
        $this->assertSame([], $dto->educationalManagerSessions);
    }

    public function testConvertDataToDTOMapsRequiredFieldsForFree(): void
    {
        $dto = Trainer::convertDataToDTO($this->makeMinimalFreeData());

        $this->assertSame('trainer-free-001', $dto->uuid);
        $this->assertSame('Leclerc', $dto->familyName);
        $this->assertSame('Paul', $dto->givenName);
        $this->assertSame('paul@example.com', $dto->email);
    }

    public function testConvertDataToDTOSetsOptionalFieldsToNullWhenAbsent(): void
    {
        $dto = Trainer::convertDataToDTO($this->makeMinimalEmployeeData());

        $this->assertNull($dto->telephone);
        $this->assertNull($dto->mobileNumber);
        $this->assertNull($dto->addressStreet);
        $this->assertNull($dto->addressPostcode);
        $this->assertNull($dto->addressLocality);
        $this->assertNull($dto->addressCountry);
        $this->assertNull($dto->image);
        $this->assertNull($dto->birthDate);
        $this->assertNull($dto->jobTitle);
        $this->assertNull($dto->society);
        $this->assertNull($dto->cv);
        $this->assertNull($dto->degree);
        $this->assertNull($dto->contract);
        $this->assertNull($dto->jobDescription);
    }

    public function testConvertDataToDTOParsesBirthDate(): void
    {
        $data = array_merge($this->makeMinimalEmployeeData(), ['birthDate' => '1985-06-15T00:00:00+00:00']);

        $dto = Trainer::convertDataToDTO($data);

        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->birthDate);
        $this->assertSame('1985-06-15', $dto->birthDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOReturnsNullBirthDateWhenFieldIsEmpty(): void
    {
        $data = array_merge($this->makeMinimalEmployeeData(), ['birthDate' => '']);

        $dto = Trainer::convertDataToDTO($data);

        $this->assertNull($dto->birthDate);
    }

    public function testConvertDataToDTOMapsAllOptionalFieldsForEmployee(): void
    {
        $data = array_merge($this->makeMinimalEmployeeData(), [
            'telephone'       => '+33611000001',
            'mobileNumber'    => '+33611000002',
            'addressStreet'   => '1 rue Test',
            'addressPostcode' => '75001',
            'addressLocality' => 'Paris',
            'addressCountry'  => 'FR',
            'image'           => 'http://img.example.com/photo.jpg',
            'birthDate'       => '1985-06-15T00:00:00+00:00',
            'jobTitle'        => 'Formateur',
            'cv'              => 'cv-content',
            'degree'          => 'Master',
            'contract'        => 'CDI',
            'jobDescription'  => 'Formation adultes',
        ]);

        $dto = Trainer::convertDataToDTO($data);

        $this->assertSame('+33611000001', $dto->telephone);
        $this->assertSame('+33611000002', $dto->mobileNumber);
        $this->assertSame('1 rue Test', $dto->addressStreet);
        $this->assertSame('75001', $dto->addressPostcode);
        $this->assertSame('Paris', $dto->addressLocality);
        $this->assertSame('FR', $dto->addressCountry);
        $this->assertSame('http://img.example.com/photo.jpg', $dto->image);
        $this->assertSame('1985-06-15', $dto->birthDate->format('Y-m-d'));
        $this->assertSame('Formateur', $dto->jobTitle);
        $this->assertSame('cv-content', $dto->cv);
        $this->assertSame('Master', $dto->degree);
        $this->assertSame('CDI', $dto->contract);
        $this->assertSame('Formation adultes', $dto->jobDescription);
    }

    public function testConvertDataToDTOMapsFreeSpecificFields(): void
    {
        $data = array_merge($this->makeMinimalFreeData(), [
            'hourlyCost'             => 75.00,
            'daylyCost'              => 600.00,
            'siret'                  => '12345678901234',
            'tvaNumber'              => 'FR12345678901',
            'urssafNumber'           => 'URSSAF123',
            'urssafCertificate'      => 'cert-url',
            'billingAddressStreet'   => '10 rue Billing',
            'billingAddressPostcode' => '69001',
            'billingAddressLocality' => 'Lyon',
            'billingAddressCountry'  => 'FR',
        ]);

        /** @var TrainerFreeOutput $dto */
        $dto = Trainer::convertDataToDTO($data);

        $this->assertInstanceOf(TrainerFreeOutput::class, $dto);
        $this->assertSame(75.00, $dto->hourlyCost);
        $this->assertSame(600.00, $dto->daylyCost);
        $this->assertSame('12345678901234', $dto->siret);
        $this->assertSame('FR12345678901', $dto->tvaNumber);
        $this->assertSame('URSSAF123', $dto->urssafNumber);
        $this->assertSame('cert-url', $dto->urssafCertificate);
        $this->assertSame('10 rue Billing', $dto->billingAddressStreet);
        $this->assertSame('69001', $dto->billingAddressPostcode);
        $this->assertSame('Lyon', $dto->billingAddressLocality);
        $this->assertSame('FR', $dto->billingAddressCountry);
    }

    public function testConvertDataToDTOMapsEmbeddedSociety(): void
    {
        $data = array_merge($this->makeMinimalEmployeeData(), [
            'society' => [
                'id'    => 'soc-uuid-001',
                'name'  => 'Société Test',
                'email' => 'contact@soc.com',
            ],
        ]);

        $dto = Trainer::convertDataToDTO($data);

        $this->assertNotNull($dto->society);
        $this->assertSame('soc-uuid-001', $dto->society->uuid);
    }

    public function testConvertDataToDTOReturnNullSocietyWhenSocietyKeyAbsent(): void
    {
        $dto = Trainer::convertDataToDTO($this->makeMinimalEmployeeData());

        $this->assertNull($dto->society);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionTrainerReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        $trainer = $this->makeTrainer($this->makeHttpClientWithResponse(200, '[]'));

        $result = $trainer->getCollectionTrainer();

        $this->assertSame([], $result);
    }

    public function testGetCollectionTrainerReturnsDTOArray(): void
    {
        $body = json_encode([
            array_merge($this->makeMinimalEmployeeData(), ['id' => 't1']),
            array_merge($this->makeMinimalFreeData(), ['id' => 't2']),
        ]);
        $trainer = $this->makeTrainer($this->makeHttpClientWithResponse(200, $body));

        $result = $trainer->getCollectionTrainer();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(TrainerOutput::class, $result[0]);
        $this->assertInstanceOf(TrainerOutput::class, $result[1]);
        $this->assertSame('t1', $result[0]->uuid);
        $this->assertSame('t2', $result[1]->uuid);
    }

    public function testGetTrainerReturnsSingleDTO(): void
    {
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalEmployeeData()))
        );

        $result = $trainer->getTrainer('trainer-emp-001');

        $this->assertInstanceOf(TrainerOutput::class, $result);
        $this->assertSame('trainer-emp-001', $result->uuid);
    }

    public function testGetTrainerThrowsExceptionWhenApiReturns404(): void
    {
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Trainer not found']))
        );

        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Trainer not found');

        $trainer->getTrainer('missing-uuid');
    }

    public function testGetTrainerThrowsExceptionWithDefaultMessageWhenNoDetail(): void
    {
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(500, json_encode([]))
        );

        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('An unexpected error occured');

        $trainer->getTrainer('some-uuid');
    }

    public function testPostTrainerEmployeeReturnsDTO(): void
    {
        $responseData = $this->makeMinimalEmployeeData();
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        $input = new TrainerEmployeeInput('Dupont', 'Jean', 'jean@example.com');

        $result = $trainer->postTrainerEmployee($input);

        $this->assertInstanceOf(TrainerOutput::class, $result);
        $this->assertSame('trainer-emp-001', $result->uuid);
    }

    public function testPostTrainerEmployeeWithBirthDate(): void
    {
        $responseData = $this->makeMinimalEmployeeData();
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        $input = new TrainerEmployeeInput(
            'Dupont',
            'Jean',
            'jean@example.com',
            true,
            true,
            Gender::GENDER_NA,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            new \DateTimeImmutable('1985-06-15')
        );

        $result = $trainer->postTrainerEmployee($input);

        $this->assertInstanceOf(TrainerOutput::class, $result);
    }

    public function testPostTrainerFreeReturnsDTO(): void
    {
        $responseData = $this->makeMinimalFreeData();
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        $input = new TrainerFreeInput('Leclerc', 'Paul', 'paul@example.com');

        $result = $trainer->postTrainerFree($input);

        $this->assertInstanceOf(TrainerOutput::class, $result);
        $this->assertSame('trainer-free-001', $result->uuid);
    }

    public function testPostTrainerFreeWithBirthDate(): void
    {
        $responseData = $this->makeMinimalFreeData();
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($responseData))
        );

        $input = new TrainerFreeInput(
            'Leclerc',
            'Paul',
            'paul@example.com',
            true,
            true,
            Gender::GENDER_NA,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            new \DateTimeImmutable('1978-03-22')
        );

        $result = $trainer->postTrainerFree($input);

        $this->assertInstanceOf(TrainerOutput::class, $result);
    }

    public function testPostTrainerEmployeeThrowsExceptionOnError(): void
    {
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(422, json_encode(['detail' => 'Invalid data']))
        );

        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Invalid data');

        $trainer->postTrainerEmployee(new TrainerEmployeeInput('F', 'G', 'bad@example.com'));
    }

    public function testPostTrainerFreeThrowsExceptionOnError(): void
    {
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(422, json_encode(['detail' => 'Validation failed']))
        );

        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Validation failed');

        $trainer->postTrainerFree(new TrainerFreeInput('F', 'G', 'bad@example.com'));
    }
}
