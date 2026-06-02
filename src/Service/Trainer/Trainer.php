<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Trainer;

use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerEmployeeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerFreeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Enum\TrainerStatut;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Society\Society;
use AgoraLearningPhp\Service\Tools\JsonHandler;

/**
 * @extends ApiService<TrainerOutput>
 */
class Trainer extends ApiService
{
    /**
     * @return array<int, TrainerOutput>
     */
    public function getCollectionTrainer(): array
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetCollectionTrainer(), $body);
        return $this->convertResponseToDTOarray($response);
    }

    public function getTrainer(string $uuid): TrainerOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetTrainer($uuid), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postTrainerEmployee(TrainerEmployeeInput $trainerEmployeeInput): TrainerOutput
    {
        $body = JsonHandler::jsonEncode(
            [
                "familyName" => $trainerEmployeeInput->familyName,
                "givenName" => $trainerEmployeeInput->givenName,
                "email" => $trainerEmployeeInput->email,
                "visibleInformation" => $trainerEmployeeInput->visibleInformation,
                "notifyUser" => $trainerEmployeeInput->notifyUser,
                "gender" => $trainerEmployeeInput->gender,
                "telephone" => $trainerEmployeeInput->telephone,
                "mobileNumber" => $trainerEmployeeInput->mobileNumber,
                "addressStreet" => $trainerEmployeeInput->addressStreet,
                "addressPostcode" => $trainerEmployeeInput->addressPostcode,
                "addressLocality" => $trainerEmployeeInput->addressLocality,
                "addressCountry" => $trainerEmployeeInput->addressCountry,
                "image" => $trainerEmployeeInput->image,
                "birthDate" => $trainerEmployeeInput->birthDate
                    ? $trainerEmployeeInput->birthDate->format(self::DATETIME_FORMAT)
                    : null,
                "jobTitle" => $trainerEmployeeInput->jobTitle,
                "societyId" => $trainerEmployeeInput->societyId,
                "cv" => $trainerEmployeeInput->cv,
                "degree" => $trainerEmployeeInput->degree,
                "contract" => $trainerEmployeeInput->contract,
                "jobDescription" => $trainerEmployeeInput->jobDescription
            ]
        );
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreateTrainerEmployee(), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postTrainerFree(TrainerFreeInput $trainerFreeInput): TrainerOutput
    {
        $body = JsonHandler::jsonEncode(
            [
                "familyName" => $trainerFreeInput->familyName,
                "givenName" => $trainerFreeInput->givenName,
                "email" => $trainerFreeInput->email,
                "visibleInformation" => $trainerFreeInput->visibleInformation,
                "notifyUser" => $trainerFreeInput->notifyUser,
                "gender" => $trainerFreeInput->gender,
                "telephone" => $trainerFreeInput->telephone,
                "mobileNumber" => $trainerFreeInput->mobileNumber,
                "addressStreet" => $trainerFreeInput->addressStreet,
                "addressPostcode" => $trainerFreeInput->addressPostcode,
                "addressLocality" => $trainerFreeInput->addressLocality,
                "addressCountry" => $trainerFreeInput->addressCountry,
                "image" => $trainerFreeInput->image,
                "birthDate" => $trainerFreeInput->birthDate
                    ? $trainerFreeInput->birthDate->format(self::DATETIME_FORMAT)
                    : null,
                "jobTitle" => $trainerFreeInput->jobTitle,
                "societyId" => $trainerFreeInput->societyId,
                "cv" => $trainerFreeInput->cv,
                "degree" => $trainerFreeInput->degree,
                "contract" => $trainerFreeInput->contract,
                "jobDescription" => $trainerFreeInput->jobDescription,
                "hourlyCost" => $trainerFreeInput->hourlyCost,
                "daylyCost" => $trainerFreeInput->daylyCost,
                "siret" => $trainerFreeInput->siret,
                "tvaNumber" => $trainerFreeInput->tvaNumber,
                "urssafNumber" => $trainerFreeInput->urssafNumber,
                "urssafCertificate" => $trainerFreeInput->urssafCertificate,
                "billingAddressStreet" => $trainerFreeInput->billingAddressStreet,
                "billingAddressPostcode" => $trainerFreeInput->billingAddressPostcode,
                "billingAddressLocality" => $trainerFreeInput->billingAddressLocality,
                "billingAddressCountry" => $trainerFreeInput->billingAddressCountry
            ]
        );
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreateTrainerFree(), $body);
        return $this->convertResponseToDTO($response);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): TrainerOutput
    {
        /** @var array{
         *     id: string,
         *     familyName: string,
         *     givenName: string,
         *     email: string,
         *     visibleInformation: bool,
         *     gender: string,
         *     educationalManagerSessions: array<int, mixed>,
         *     telephone?: string|null,
         *     mobileNumber?: string|null,
         *     addressStreet?: string|null,
         *     addressPostcode?: string|null,
         *     addressLocality?: string|null,
         *     addressCountry?: string|null,
         *     birthDate?: string|null,
         *     jobTitle?: string|null,         
         *     statut?: string|null,
         *     society?: array<string, mixed>,
         *     hourlyCost?: float|null,
         *     daylyCost?: float|null,
         *     siret?: string|null,
         *     tvaNumber?: string|null,
         *     urssafNumber?: string|null,
         *     billingAddressStreet?: string|null,
         *     billingAddressPostcode?: string|null,
         *     billingAddressLocality?: string|null,
         *     billingAddressCountry?: string|null
         * } $data */
        $telephone = $data['telephone'] ?? null;
        $mobileNumber = $data['mobileNumber'] ?? null;
        $addressStreet = $data['addressStreet'] ?? null;
        $addressPostcode = $data['addressPostcode'] ?? null;
        $addressLocality = $data['addressLocality'] ?? null;
        $addressCountry = $data['addressCountry'] ?? null;
        $birthDate = !empty($data['birthDate']) ? new \DateTimeImmutable($data['birthDate']) : null;
        $jobTitle = $data['jobTitle'] ?? null;
        $societyData = array_key_exists('society', $data) ? Society::convertDataToDTO($data['society']) : null;

        switch ($data['statut'] ?? null) {
            case TrainerStatut::STATUT_FREE:
                $hourlyCost = $data['hourlyCost'] ?? null;
                $daylyCost = $data['daylyCost'] ?? null;
                $siret = $data['siret'] ?? null;
                $tvaNumber = $data['tvaNumber'] ?? null;
                $urssafNumber = $data['urssafNumber'] ?? null;
                $billingAddressStreet = $data['billingAddressStreet'] ?? null;
                $billingAddressPostcode = $data['billingAddressPostcode'] ?? null;
                $billingAddressLocality = $data['billingAddressLocality'] ?? null;
                $billingAddressCountry = $data['billingAddressCountry'] ?? null;
                return new TrainerFreeOutput(
                    $data['id'],
                    $data['familyName'],
                    $data['givenName'],
                    $data['email'],
                    $data['visibleInformation'],
                    $data['gender'],
                    $data['educationalManagerSessions'],
                    $telephone,
                    $mobileNumber,
                    $addressStreet,
                    $addressPostcode,
                    $addressLocality,
                    $addressCountry,
                    $birthDate,
                    $jobTitle,
                    $societyData,
                    $hourlyCost,
                    $daylyCost,
                    $siret,
                    $tvaNumber,
                    $urssafNumber,
                    $billingAddressStreet,
                    $billingAddressPostcode,
                    $billingAddressLocality,
                    $billingAddressCountry
                );
            case TrainerStatut::STATUT_EMPLOYE:
            default:
                return new TrainerEmployeeOutput(
                    $data['id'],
                    $data['familyName'],
                    $data['givenName'],
                    $data['email'],
                    $data['visibleInformation'],
                    $data['gender'],
                    $data['educationalManagerSessions'],
                    $telephone,
                    $mobileNumber,
                    $addressStreet,
                    $addressPostcode,
                    $addressLocality,
                    $addressCountry,
                    $birthDate,
                    $jobTitle,
                    $societyData
                );
        }
    }
}
