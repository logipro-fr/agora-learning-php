<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Person;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use AgoraLearningPhp\Identifier\ExternalId;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Tools\JsonHandler;

/**
 * @extends ApiService<PersonOutput>
 */
class Person extends ApiService
{
    /**
     * @return array<int, PersonOutput>
     */
    public function getCollectionPerson(): array
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetCollectionPerson(), $body);
        return $this->convertResponseToDTOarray($response);
    }

    public function getPerson(ApiObjectIdentifier $identifier): PersonOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetPerson($identifier->getId()), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postPerson(PersonInput $personInput): CreatedOutput
    {
        $body = JsonHandler::jsonEncode(
            [
                "externalIdentifier" => $personInput->externalIdentifier->toArray(),
                "familyName" => $personInput->familyName,
                "givenName" => $personInput->givenName,
                "email" => $personInput->email,
                "gender" => $personInput->gender,
                "birthDate" => $personInput->birthDate ? $personInput->birthDate->format(self::DATETIME_FORMAT) : null,
                "jobTitle" => $personInput->jobTitle,
                "addressStreet" => $personInput->addressStreet,
                "addressPostcode" => $personInput->addressPostcode,
                "addressLocality" => $personInput->addressLocality,
                "addressCountry" => $personInput->addressCountry,
                "telephone" => $personInput->telephone,
                "image" => $personInput->image
            ]
        );
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreatePerson(), $body);
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): PersonOutput
    {
        /** @var array{
         *     agoraId: string,
         *     externalIdentifier?: array<string, mixed>|null,
         *     familyName: string,
         *     givenName: string,
         *     email: string,
         *     telephone?: string|null,
         *     addressStreet?: string|null,
         *     addressPostcode?: string|null,
         *     addressLocality?: string|null,
         *     addressCountry?: string|null,
         *     gender?: string,
         *     birthDate?: string|null,
         *     jobTitle?: string|null
         * } $data */
        $telephone = $data['telephone'] ?? null;
        $addressStreet = $data['addressStreet'] ?? null;
        $addressPostcode = $data['addressPostcode'] ?? null;
        $addressLocality = $data['addressLocality'] ?? null;
        $addressCountry = $data['addressCountry'] ?? null;
        $gender = $data['gender'] ?? Gender::GENDER_NA;
        $birthDate = !empty($data['birthDate']) ? new \DateTimeImmutable($data['birthDate']) : null;
        $jobTitle = $data['jobTitle'] ?? null;
        $externalIdentifier = !empty($data['externalIdentifier']) ? ExternalId::fromArray($data['externalIdentifier']) : null;

        return new PersonOutput(
            $data['agoraId'],
            $data['familyName'],
            $data['givenName'],
            $data['email'],
            $telephone,
            $addressStreet,
            $addressPostcode,
            $addressLocality,
            $addressCountry,
            $gender,
            $birthDate,
            $jobTitle,
            $externalIdentifier,
        );
    }
}
