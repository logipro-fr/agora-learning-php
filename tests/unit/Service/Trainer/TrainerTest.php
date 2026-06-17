<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\Trainer;

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
use AgoraLearningPhp\Tests\unit\Service\ServiceTestCase;

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
        // Act
        $dto = Trainer::convertDataToDTO($this->makeMinimalEmployeeData());

        // Assert
        $this->assertInstanceOf(TrainerEmployeeOutput::class, $dto);
    }

    public function testConvertDataToDTOReturnsTrainerFreeOutputWhenStatutIsFree(): void
    {
        // Act
        $dto = Trainer::convertDataToDTO($this->makeMinimalFreeData());

        // Assert
        $this->assertInstanceOf(TrainerFreeOutput::class, $dto);
    }

    public function testConvertDataToDTODefaultsToEmployeeWhenStatutIsMissing(): void
    {
        // Arrange
        $data = $this->makeMinimalEmployeeData();
        unset($data['statut']);

        // Act
        $dto = Trainer::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(TrainerEmployeeOutput::class, $dto);
    }

    public function testConvertDataToDTOMapsRequiredFieldsForEmployee(): void
    {
        // Act
        $dto = Trainer::convertDataToDTO($this->makeMinimalEmployeeData());

        // Assert
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
        // Act
        $dto = Trainer::convertDataToDTO($this->makeMinimalFreeData());

        // Assert
        $this->assertSame('trainer-free-001', $dto->uuid);
        $this->assertSame('Leclerc', $dto->familyName);
        $this->assertSame('Paul', $dto->givenName);
        $this->assertSame('paul@example.com', $dto->email);
    }

    public function testConvertDataToDTOSetsOptionalFieldsToNullWhenAbsent(): void
    {
        // Act
        $dto = Trainer::convertDataToDTO($this->makeMinimalEmployeeData());

        // Assert
        $this->assertNull($dto->telephone);
        $this->assertNull($dto->mobileNumber);
        $this->assertNull($dto->addressStreet);
        $this->assertNull($dto->addressPostcode);
        $this->assertNull($dto->addressLocality);
        $this->assertNull($dto->addressCountry);
        $this->assertNull($dto->birthDate);
        $this->assertNull($dto->jobTitle);
        $this->assertNull($dto->society);
    }

    public function testConvertDataToDTOParsesBirthDate(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalEmployeeData(), ['birthDate' => '1985-06-15T00:00:00+00:00']);

        // Act
        $dto = Trainer::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->birthDate);
        $this->assertSame('1985-06-15', $dto->birthDate->format('Y-m-d'));
    }

    public function testConvertDataToDTOReturnsNullBirthDateWhenFieldIsEmpty(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalEmployeeData(), ['birthDate' => '']);

        // Act
        $dto = Trainer::convertDataToDTO($data);

        // Assert
        $this->assertNull($dto->birthDate);
    }

    public function testConvertDataToDTOMapsAllOptionalFieldsForEmployee(): void
    {
        // Arrange
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

        // Act
        $dto = Trainer::convertDataToDTO($data);

        // Assert
        $this->assertSame('+33611000001', $dto->telephone);
        $this->assertSame('+33611000002', $dto->mobileNumber);
        $this->assertSame('1 rue Test', $dto->addressStreet);
        $this->assertSame('75001', $dto->addressPostcode);
        $this->assertSame('Paris', $dto->addressLocality);
        $this->assertSame('FR', $dto->addressCountry);
        $this->assertSame('1985-06-15', $dto->birthDate->format('Y-m-d'));
        $this->assertSame('Formateur', $dto->jobTitle);
    }

    public function testConvertDataToDTOMapsFreeSpecificFields(): void
    {
        // Arrange
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

        // Act
        /** @var TrainerFreeOutput $dto */
        $dto = Trainer::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(TrainerFreeOutput::class, $dto);
        $this->assertSame(75.00, $dto->hourlyCost);
        $this->assertSame(600.00, $dto->daylyCost);
        $this->assertSame('12345678901234', $dto->siret);
        $this->assertSame('FR12345678901', $dto->tvaNumber);
        $this->assertSame('URSSAF123', $dto->urssafNumber);
        $this->assertSame('10 rue Billing', $dto->billingAddressStreet);
        $this->assertSame('69001', $dto->billingAddressPostcode);
        $this->assertSame('Lyon', $dto->billingAddressLocality);
        $this->assertSame('FR', $dto->billingAddressCountry);
    }

    public function testConvertDataToDTOMapsEmbeddedSociety(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalEmployeeData(), [
            'society' => [
                'id'    => 'soc-uuid-001',
                'name'  => 'Société Test',
                'email' => 'contact@soc.com',
            ],
        ]);

        // Act
        $dto = Trainer::convertDataToDTO($data);

        // Assert
        $this->assertNotNull($dto->society);
        $this->assertSame('soc-uuid-001', $dto->society->uuid);
    }

    public function testConvertDataToDTOReturnNullSocietyWhenSocietyKeyAbsent(): void
    {
        // Act
        $dto = Trainer::convertDataToDTO($this->makeMinimalEmployeeData());

        // Assert
        $this->assertNull($dto->society);
    }

    public function testConvertDataToDTOWithStatutEmployeReturnsTrainerEmployeeOutput(): void
    {
        // Arrange
        $data = array_merge($this->makeMinimalEmployeeData(), ['statut' => TrainerStatut::STATUT_EMPLOYE]);

        // Act
        $dto = Trainer::convertDataToDTO($data);

        // Assert
        $this->assertInstanceOf(TrainerEmployeeOutput::class, $dto);
        $this->assertSame('trainer-emp-001', $dto->uuid);
    }

    // -------------------------------------------------------------------------
    // Méthodes HTTP — réseau simulé
    // -------------------------------------------------------------------------

    public function testGetCollectionTrainerReturnsEmptyArrayWhenApiReturnsEmptyCollection(): void
    {
        // Arrange
        $trainer = $this->makeTrainer($this->makeHttpClientWithResponse(200, '[]'));

        // Act
        $result = $trainer->getCollectionTrainer();

        // Assert
        $this->assertSame([], $result);
    }

    public function testGetCollectionTrainerReturnsDTOArray(): void
    {
        // Arrange
        $body    = json_encode([
            array_merge($this->makeMinimalEmployeeData(), ['id' => 't1']),
            array_merge($this->makeMinimalFreeData(), ['id' => 't2']),
        ]);
        $trainer = $this->makeTrainer($this->makeHttpClientWithResponse(200, $body));

        // Act
        $result = $trainer->getCollectionTrainer();

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(TrainerOutput::class, $result[0]);
        $this->assertInstanceOf(TrainerOutput::class, $result[1]);
        $this->assertSame('t1', $result[0]->uuid);
        $this->assertSame('t2', $result[1]->uuid);
    }

    public function testGetTrainerReturnsSingleDTO(): void
    {
        // Arrange
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalEmployeeData()))
        );

        // Act
        $result = $trainer->getTrainer('trainer-emp-001');

        // Assert
        $this->assertInstanceOf(TrainerOutput::class, $result);
        $this->assertSame('trainer-emp-001', $result->uuid);
    }

    public function testGetTrainerThrowsExceptionWhenApiReturns404(): void
    {
        // Arrange
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(404, json_encode(['detail' => 'Trainer not found']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Trainer not found');

        // Act
        $trainer->getTrainer('missing-uuid');
    }

    public function testGetTrainerThrowsExceptionWithDefaultMessageWhenNoDetail(): void
    {
        // Arrange
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(500, json_encode([]))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('An unexpected error occured');

        // Act
        $trainer->getTrainer('some-uuid');
    }

    public function testPostTrainerEmployeeReturnsDTO(): void
    {
        // Arrange
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalEmployeeData()))
        );
        $input = new TrainerEmployeeInput('Dupont', 'Jean', 'jean@example.com');

        // Act
        $result = $trainer->postTrainerEmployee($input);

        // Assert
        $this->assertInstanceOf(TrainerOutput::class, $result);
        $this->assertSame('trainer-emp-001', $result->uuid);
    }

    public function testPostTrainerEmployeeWithBirthDate(): void
    {
        // Arrange
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalEmployeeData()))
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

        // Act
        $result = $trainer->postTrainerEmployee($input);

        // Assert
        $this->assertInstanceOf(TrainerOutput::class, $result);
    }

    public function testPostTrainerFreeReturnsDTO(): void
    {
        // Arrange
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalFreeData()))
        );
        $input = new TrainerFreeInput('Leclerc', 'Paul', 'paul@example.com');

        // Act
        $result = $trainer->postTrainerFree($input);

        // Assert
        $this->assertInstanceOf(TrainerOutput::class, $result);
        $this->assertSame('trainer-free-001', $result->uuid);
    }

    public function testPostTrainerFreeWithBirthDate(): void
    {
        // Arrange
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(200, json_encode($this->makeMinimalFreeData()))
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

        // Act
        $result = $trainer->postTrainerFree($input);

        // Assert
        $this->assertInstanceOf(TrainerOutput::class, $result);
    }

    public function testPostTrainerEmployeeThrowsExceptionOnError(): void
    {
        // Arrange
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(422, json_encode(['detail' => 'Invalid data']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Invalid data');

        // Act
        $trainer->postTrainerEmployee(new TrainerEmployeeInput('F', 'G', 'bad@example.com'));
    }

    public function testPostTrainerFreeThrowsExceptionOnError(): void
    {
        // Arrange
        $trainer = $this->makeTrainer(
            $this->makeHttpClientWithResponse(422, json_encode(['detail' => 'Validation failed']))
        );

        // Assert
        $this->expectException(AgoraLearningClientException::class);
        $this->expectExceptionMessage('Validation failed');

        // Act
        $trainer->postTrainerFree(new TrainerFreeInput('F', 'G', 'bad@example.com'));
    }

    public function testPostTrainerEmployeeSendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $trainer      = $this->makeTrainer(
            $this->makeHttpClientInspectingBody(200, json_encode($this->makeMinimalEmployeeData()), $capturedBody)
        );
        $input = new TrainerEmployeeInput(
            'Dupont',
            'Jean',
            'jean@example.com',
            true,
            false,
            Gender::GENDER_MALE,
            '+33611000001',
            '+33611000002',
            '1 rue Test',
            '75001',
            'Paris',
            'FR',
            'http://img.example.com/photo.jpg',
            new \DateTimeImmutable('1985-06-15'),
            'Formateur',
            'society-uuid-001',
            'cv-content',
            'Master',
            'CDI',
            'Formation adultes'
        );

        // Act
        $trainer->postTrainerEmployee($input);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame('Dupont', $body['familyName']);
        $this->assertSame('Jean', $body['givenName']);
        $this->assertSame('jean@example.com', $body['email']);
        $this->assertTrue($body['visibleInformation']);
        $this->assertFalse($body['notifyUser']);
        $this->assertSame(Gender::GENDER_MALE, $body['gender']);
        $this->assertSame('+33611000001', $body['telephone']);
        $this->assertSame('+33611000002', $body['mobileNumber']);
        $this->assertSame('1 rue Test', $body['addressStreet']);
        $this->assertSame('75001', $body['addressPostcode']);
        $this->assertSame('Paris', $body['addressLocality']);
        $this->assertSame('FR', $body['addressCountry']);
        $this->assertSame('http://img.example.com/photo.jpg', $body['image']);
        $this->assertArrayHasKey('birthDate', $body);
        $this->assertSame('Formateur', $body['jobTitle']);
        $this->assertSame('society-uuid-001', $body['societyId']);
        $this->assertSame('cv-content', $body['cv']);
        $this->assertSame('Master', $body['degree']);
        $this->assertSame('CDI', $body['contract']);
        $this->assertSame('Formation adultes', $body['jobDescription']);
    }

    public function testPostTrainerFreeSendsAllFieldsInBody(): void
    {
        // Arrange
        $capturedBody = '';
        $trainer      = $this->makeTrainer(
            $this->makeHttpClientInspectingBody(200, json_encode($this->makeMinimalFreeData()), $capturedBody)
        );
        $input = new TrainerFreeInput(
            'Leclerc',
            'Paul',
            'paul@example.com',
            true,
            true,
            Gender::GENDER_NA,
            '+33622000001',
            null,
            '10 rue Libre',
            '69001',
            'Lyon',
            'FR',
            null,
            null,
            'Consultant',
            null,
            null,
            null,
            null,
            null,
            75.0,
            600.0,
            '12345678901234',
            'FR12345678901',
            'URSSAF123',
            'cert-url',
            '10 rue Billing',
            '69002',
            'Lyon',
            'FR'
        );

        // Act
        $trainer->postTrainerFree($input);

        // Assert
        $body = json_decode($capturedBody, true);
        $this->assertSame('Leclerc', $body['familyName']);
        $this->assertSame('Paul', $body['givenName']);
        $this->assertSame('paul@example.com', $body['email']);
        $this->assertSame('+33622000001', $body['telephone']);
        $this->assertSame('10 rue Libre', $body['addressStreet']);
        $this->assertSame('69001', $body['addressPostcode']);
        $this->assertSame('Lyon', $body['addressLocality']);
        $this->assertSame('FR', $body['addressCountry']);
        $this->assertSame('Consultant', $body['jobTitle']);
        $this->assertEquals(75.0, $body['hourlyCost']);
        $this->assertEquals(600.0, $body['daylyCost']);
        $this->assertSame('12345678901234', $body['siret']);
        $this->assertSame('FR12345678901', $body['tvaNumber']);
        $this->assertSame('URSSAF123', $body['urssafNumber']);
        $this->assertSame('cert-url', $body['urssafCertificate']);
        $this->assertSame('10 rue Billing', $body['billingAddressStreet']);
        $this->assertSame('69002', $body['billingAddressPostcode']);
        $this->assertSame('Lyon', $body['billingAddressLocality']);
        $this->assertSame('FR', $body['billingAddressCountry']);
    }
}
