<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Learner;

use AgoraLearningPhp\DTO\Input\Learner\LearnerInput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Tools\JsonHandler;

/**
 * @extends ApiService<LearnerOutput>
 */
class Learner extends ApiService
{
    /**
     * @return array<int, LearnerOutput>
     */
    public function getCollectionLearner(): array
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetCollectionLearner(), $body);
        return $this->convertResponseToDTOarray($response);
    }

    public function getLearner(string $uuid): LearnerOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetLearner($uuid), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postLearner(LearnerInput $learnerInput): LearnerOutput
    {
        $body = JsonHandler::jsonEncode(
            [
                "familyName" => $learnerInput->familyName,
                "givenName" => $learnerInput->givenName,
                "gender" => $learnerInput->gender,
                "recoverEmail" => $learnerInput->recoverEmail,
                "email" => $learnerInput->email,
                "birthDate" => $learnerInput->birthDate ? $learnerInput->birthDate->format(self::DATETIME_FORMAT) : null,
                "jobTitle" => $learnerInput->jobTitle,
                "addressStreet" => $learnerInput->addressStreet,
                "addressPostcode" => $learnerInput->addressPostcode,
                "addressLocality" => $learnerInput->addressLocality,
                "addressCountry" => $learnerInput->addressCountry,
                "telephone" => $learnerInput->telephone,
                "image" => $learnerInput->image
            ]
        );
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreateLearner(), $body);
        return $this->convertResponseToDTO($response);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): LearnerOutput
    {
        /** @var array{
         *     id: string,
         *     username: string,
         *     familyName: string,
         *     givenName: string,
         *     recoverEmail: string,
         *     email: string,
         *     gender?: string,         
         *     telephone?: string|null,
         *     addressStreet?: string|null,
         *     addressPostcode?: string|null,
         *     addressLocality?: string|null,
         *     addressCountry?: string|null,         
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

        return new LearnerOutput(
            $data['id'],
            $data['username'],
            $data['familyName'],
            $data['givenName'],
            $data['recoverEmail'],
            $data['email'],
            $gender,
            $telephone,
            $addressStreet,
            $addressPostcode,
            $addressLocality,
            $addressCountry,
            $birthDate,
            $jobTitle
        );
    }
}
