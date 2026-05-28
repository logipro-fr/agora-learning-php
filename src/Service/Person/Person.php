<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Person;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Tools\JsonHandler;

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

    public function getPerson(string $uuid): PersonOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetPerson($uuid), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postPerson(PersonInput $personInput): PersonOutput
    {
        $body = JsonHandler::jsonEncode(
            [
                "familyName" => $personInput->familyName,
                "givenName" => $personInput->givenName,
                "gender" => $personInput->gender,
                "birthDate" => $personInput->birthDate ? $personInput->birthDate->format(self::DATETIME_FORMAT) : null,
                "jobTitle" => $personInput->jobTitle,
                "addressStreet" => $personInput->addressStreet,
                "addressPostcode" => $personInput->addressPostcode,
                "addressLocality" => $personInput->addressLocality,
                "addressCountry" => $personInput->addressCountry,
                "telephone" => $personInput->telephone,
                "email" => $personInput->email,
                "image" => $personInput->image
            ]
        );
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreatePerson(), $body);
        return $this->convertResponseToDTO($response);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): PersonOutput
    {
        $email = $data['email'] ?? null;
        $telephone = $data['telephone'] ?? null;
        $addressStreet = $data['addressStreet'] ?? null;
        $addressPostcode = $data['addressPostcode'] ?? null;
        $addressLocality = $data['addressLocality'] ?? null;
        $addressCountry = $data['addressCountry'] ?? null;
        $image = $data['image'] ?? null;
        $gender = $data['gender'] ?? Gender::GENDER_NA;
        $birthDate = !empty($data['birthDate']) ? new \DateTimeImmutable($data['birthDate']) : null;
        $jobTitle = $data['jobTitle'] ?? null;

        return new PersonOutput(
            $data['id'],
            $data['familyName'],
            $data['givenName'],
            $email,
            $telephone,
            $addressStreet,
            $addressPostcode,
            $addressLocality,
            $addressCountry,
            $image,
            $gender,
            $birthDate,
            $jobTitle,
        );
    }
}
