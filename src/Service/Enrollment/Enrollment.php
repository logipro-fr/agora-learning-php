<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Enrollment;

use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use AgoraLearningPhp\DTO\Output\Enrollment\EnrollmentOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Learner\Learner;
use AgoraLearningPhp\Service\Session\Session;
use AgoraLearningPhp\Service\Tools\JsonHandler;

/**
 * @extends ApiService<EnrollmentOutput>
 */
class Enrollment extends ApiService
{
    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromSession(ApiObjectIdentifier $sessionIdentifier): array
    {
        $body = '';
        $response = $this->httpClient->request(
            RequestMethod::GET,
            ApiUrls::getGetCollectionEnrollmentFromSession($sessionIdentifier->getId()),
            $body
        );
        return $this->convertResponseToDTOarray($response);
    }

    /**
     * @return array<int, EnrollmentOutput>
     */
    public function getCollectionEnrollmentFromLearner(ApiObjectIdentifier $learnerIdentifier): array
    {
        $body = '';
        $response = $this->httpClient->request(
            RequestMethod::GET,
            ApiUrls::getGetCollectionEnrollmentFromLearner($learnerIdentifier->getId()),
            $body
        );
        return $this->convertResponseToDTOarray($response);
    }

    public function getEnrollment(ApiObjectIdentifier $identifier): EnrollmentOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetEnrollment($identifier->getId()), $body);
        return $this->convertResponseToDTO($response);
    }

    public function getEnrollmentFromSessionAndLearner(
        ApiObjectIdentifier $sessionIdentifier,
        ApiObjectIdentifier $learnerIdentifier
    ): EnrollmentOutput {
        $body = '';
        $response = $this->httpClient->request(
            RequestMethod::GET,
            ApiUrls::getGetEnrollmentFromSessionAndLearner($sessionIdentifier->getId(), $learnerIdentifier->getId()),
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
        /** @var array{
         *     id: string,
         *     learner: array<string, mixed>,
         *     session: array<string, mixed>,
         *     availabilityStartDate?: string|null,
         *     availabilityEndDate?: string|null
         * } $data */
        $learner = Learner::convertDataToDTO($data['learner']);
        $session = Session::convertDataToDTO($data['session']);
        $availabilityStartDate = !empty($data['availabilityStartDate']) ? new \DateTimeImmutable($data['availabilityStartDate']) : null;
        $availabilityEndDate = !empty($data['availabilityEndDate']) ? new \DateTimeImmutable($data['availabilityEndDate']) : null;

        return new EnrollmentOutput(
            $data['id'],
            $learner,
            $session,
            $availabilityStartDate,
            $availabilityEndDate
        );
    }
}
