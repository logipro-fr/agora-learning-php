<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Enrollment;

use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\DTO\Output\Enrollment\EnrollmentOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Person\Person;
use AgoraLearningPhp\Service\Session\Session;
use AgoraLearningPhp\Service\Tools\JsonHandler;

class Enrollment extends ApiService
{
    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromSession(string $sessionUuid): array
    {
        $body = '';
        $response = $this->httpClient->request(
            RequestMethod::GET,
            ApiUrls::getGetCollectionEnrollmentFromSession($sessionUuid),
            $body
        );
        return $this->convertResponseToDTOarray($response);
    }

    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromLearner(string $learnerUuid): array
    {
        $body = '';
        $response = $this->httpClient->request(
            RequestMethod::GET,
            ApiUrls::getGetCollectionEnrollmentFromLearner($learnerUuid),
            $body
        );
        return $this->convertResponseToDTOarray($response);
    }

    public function getEnrollment(string $uuid): EnrollmentOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetEnrollment($uuid), $body);
        return $this->convertResponseToDTO($response);
    }

    public function getEnrollmentFromSessionAndLearner(string $sessionUuid, string $learnerUuid): EnrollmentOutput
    {
        $body = '';
        $response = $this->httpClient->request(
            RequestMethod::GET,
            ApiUrls::getGetEnrollmentFromSessionAndLearner($sessionUuid, $learnerUuid),
            $body
        );
        return $this->convertResponseToDTO($response);
    }

    public function postEnrollment(EnrollmentInput $enrollmentInput): EnrollmentOutput
    {
        $data = [
            'sessionId' => $enrollmentInput->sessionUuid,
            'learnerId' => $enrollmentInput->learnerUuid,
            'sendingInvite' => $enrollmentInput->sendingInvite
        ];
        $body = JsonHandler::jsonEncode($data);
        $response = $this->httpClient->request(RequestMethod::POST, ApiUrls::getCreateEnrollment(), $body);
        return $this->convertResponseToDTO($response);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): EnrollmentOutput
    {
        $learner = Person::convertDataToDTO($data['learner']);
        $session = Session::convertDataToDTO($data['session']);
        $availabilityStartDate = new \DateTimeImmutable($data['availabilityStartDate']);
        $availabilityEndDate = new \DateTimeImmutable($data['availabilityEndDate']);

        return new EnrollmentOutput(
            $data['id'],
            $learner,
            $session,
            $availabilityStartDate,
            $availabilityEndDate
        );
    }
}
