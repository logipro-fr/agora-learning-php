<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Trainer;

use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerEmployeePatchInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreeInput;
use AgoraLearningPhp\DTO\Input\Trainer\TrainerFreePatchInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerEmployeeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerFreeOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Enum\TrainerStatut;
use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use AgoraLearningPhp\Identifier\ExternalId;
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

    public function getTrainer(ApiObjectIdentifier $identifier): TrainerOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetTrainer($identifier->getId()), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postTrainerEmployee(TrainerEmployeeInput $trainerEmployeeInput): CreatedOutput
    {
        $body = JsonHandler::jsonEncode($this->buildEmployeeBody($trainerEmployeeInput));
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreateTrainerEmployee(), $body);
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Remplace le formateur employé (PUT) : un champ facultatif non renseigné est vidé.
     * notifyUser est ignoré par l'API (pris en compte uniquement à la création).
     */
    public function putTrainerEmployee(ApiObjectIdentifier $identifier, TrainerEmployeeInput $trainerEmployeeInput): CreatedOutput
    {
        $body = JsonHandler::jsonEncode($this->buildEmployeeBody($trainerEmployeeInput));
        $response = $this->httpClient->request(
            RequestMethod::PUT,
            ApiUrls::getUpdateTrainerEmployee($identifier->getId()),
            $body
        );
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Modifie partiellement le formateur employé (PATCH) : seuls les champs renseignés sont modifiés.
     */
    public function patchTrainerEmployee(ApiObjectIdentifier $identifier, TrainerEmployeePatchInput $patchInput): CreatedOutput
    {
        return $this->sendPatch(ApiUrls::getUpdateTrainerEmployee($identifier->getId()), $patchInput);
    }

    public function postTrainerFree(TrainerFreeInput $trainerFreeInput): CreatedOutput
    {
        $body = JsonHandler::jsonEncode($this->buildFreeBody($trainerFreeInput));
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreateTrainerFree(), $body);
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Remplace le formateur indépendant (PUT) : un champ facultatif non renseigné est vidé.
     * notifyUser est ignoré par l'API (pris en compte uniquement à la création).
     */
    public function putTrainerFree(ApiObjectIdentifier $identifier, TrainerFreeInput $trainerFreeInput): CreatedOutput
    {
        $body = JsonHandler::jsonEncode($this->buildFreeBody($trainerFreeInput));
        $response = $this->httpClient->request(RequestMethod::PUT, ApiUrls::getUpdateTrainerFree($identifier->getId()), $body);
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Modifie partiellement le formateur indépendant (PATCH) : seuls les champs renseignés sont modifiés.
     */
    public function patchTrainerFree(ApiObjectIdentifier $identifier, TrainerFreePatchInput $patchInput): CreatedOutput
    {
        return $this->sendPatch(ApiUrls::getUpdateTrainerFree($identifier->getId()), $patchInput);
    }

    /**
     * Corps commun à la création (POST) et au remplacement (PUT) d'un formateur employé.
     *
     * @return array<string, mixed>
     */
    private function buildEmployeeBody(TrainerEmployeeInput $input): array
    {
        return [
            "externalIdentifier" => $input->externalIdentifier->toArray(),
            "familyName" => $input->familyName,
            "givenName" => $input->givenName,
            "email" => $input->email,
            "visibleInformation" => $input->visibleInformation,
            "notifyUser" => $input->notifyUser,
            "gender" => $input->gender,
            "telephone" => $input->telephone,
            "mobileNumber" => $input->mobileNumber,
            "addressStreet" => $input->addressStreet,
            "addressPostcode" => $input->addressPostcode,
            "addressLocality" => $input->addressLocality,
            "addressCountry" => $input->addressCountry,
            "image" => $input->image,
            "birthDate" => $input->birthDate
                ? $input->birthDate->format(self::DATETIME_FORMAT)
                : null,
            "jobTitle" => $input->jobTitle,
            "societyId" => $input->societyId,
            "cv" => $input->cv,
            "degree" => $input->degree,
            "contract" => $input->contract,
            "jobDescription" => $input->jobDescription
        ];
    }

    /**
     * Corps commun à la création (POST) et au remplacement (PUT) d'un formateur indépendant.
     *
     * @return array<string, mixed>
     */
    private function buildFreeBody(TrainerFreeInput $input): array
    {
        return [
            "externalIdentifier" => $input->externalIdentifier->toArray(),
            "familyName" => $input->familyName,
            "givenName" => $input->givenName,
            "email" => $input->email,
            "visibleInformation" => $input->visibleInformation,
            "notifyUser" => $input->notifyUser,
            "gender" => $input->gender,
            "telephone" => $input->telephone,
            "mobileNumber" => $input->mobileNumber,
            "addressStreet" => $input->addressStreet,
            "addressPostcode" => $input->addressPostcode,
            "addressLocality" => $input->addressLocality,
            "addressCountry" => $input->addressCountry,
            "image" => $input->image,
            "birthDate" => $input->birthDate
                ? $input->birthDate->format(self::DATETIME_FORMAT)
                : null,
            "jobTitle" => $input->jobTitle,
            "societyId" => $input->societyId,
            "cv" => $input->cv,
            "degree" => $input->degree,
            "contract" => $input->contract,
            "jobDescription" => $input->jobDescription,
            "hourlyCost" => $input->hourlyCost,
            "daylyCost" => $input->daylyCost,
            "siret" => $input->siret,
            "tvaNumber" => $input->tvaNumber,
            "urssafNumber" => $input->urssafNumber,
            "urssafCertificate" => $input->urssafCertificate,
            "billingAddressStreet" => $input->billingAddressStreet,
            "billingAddressPostcode" => $input->billingAddressPostcode,
            "billingAddressLocality" => $input->billingAddressLocality,
            "billingAddressCountry" => $input->billingAddressCountry
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): TrainerOutput
    {
        /** @var array{
         *     agoraId: string,
         *     externalIdentifier?: array<string, mixed>|null,
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
        $externalIdentifier = !empty($data['externalIdentifier']) ? ExternalId::fromArray($data['externalIdentifier']) : null;
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
                    $data['agoraId'],
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
                    $billingAddressCountry,
                    $externalIdentifier,
                );
            case TrainerStatut::STATUT_EMPLOYE:
            default:
                return new TrainerEmployeeOutput(
                    $data['agoraId'],
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
                    $externalIdentifier,
                );
        }
    }
}
