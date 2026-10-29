<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Session;

use AgoraLearningPhp\DTO\Input\Session\AbstractSessionPatchInput;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\FixedSessionPatchInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionPatchInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use AgoraLearningPhp\Identifier\ExternalId;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Person\Person;
use AgoraLearningPhp\Service\Tools\JsonHandler;
use AgoraLearningPhp\Service\Trainer\Trainer;

/**
 * @extends ApiService<SessionOutput>
 */
class Session extends ApiService
{
    /**
     * @return array<int, SessionOutput>
     */
    public function getCollectionSession(): array
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetCollectionSession(), $body);
        return $this->convertResponseToDTOarray($response);
    }

    public function getSession(ApiObjectIdentifier $identifier): SessionOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetSession($identifier->getId()), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postSession(SessionInput $sessionInput): CreatedOutput
    {
        $url = $sessionInput->sessionData instanceof OpenedSessionInput
            ? ApiUrls::getCreateOpenedSession()
            : ApiUrls::getCreateFixedSession();
        $body = JsonHandler::jsonEncode($this->buildBody($sessionInput));
        $response = $this->httpClient->request(RequestMethod::POST, $url, $body);
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Remplace la session (PUT) : un champ facultatif non renseigné est vidé.
     * La route dépend du type de $sessionInput->sessionData, qui doit correspondre au type
     * de la session existante (sinon HTTP 404) : le type d'une session ne peut pas être changé.
     */
    public function putSession(ApiObjectIdentifier $identifier, SessionInput $sessionInput): CreatedOutput
    {
        $url = $sessionInput->sessionData instanceof OpenedSessionInput
            ? ApiUrls::getUpdateOpenedSession($identifier->getId())
            : ApiUrls::getUpdateFixedSession($identifier->getId());
        $body = JsonHandler::jsonEncode($this->buildBody($sessionInput));
        $response = $this->httpClient->request(RequestMethod::PUT, $url, $body);
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Modifie partiellement la session (PATCH) : seuls les champs renseignés sont modifiés.
     * La route dépend du type de $sessionPatchInput (FixedSessionPatchInput ou OpenedSessionPatchInput).
     */
    public function patchSession(ApiObjectIdentifier $identifier, AbstractSessionPatchInput $sessionPatchInput): CreatedOutput
    {
        if ($sessionPatchInput instanceof FixedSessionPatchInput) {
            $url = ApiUrls::getUpdateFixedSession($identifier->getId());
        } elseif ($sessionPatchInput instanceof OpenedSessionPatchInput) {
            $url = ApiUrls::getUpdateOpenedSession($identifier->getId());
        } else {
            throw new \InvalidArgumentException('Unsupported session patch type: ' . get_class($sessionPatchInput));
        }

        return $this->sendPatch($url, $sessionPatchInput);
    }

    /**
     * Détache la session de l'application source en retirant son identifiant externe : elle reste sur Agora
     * mais n'est plus liée à l'application qui l'a créée. La route de modification dépendant du type, la
     * session est d'abord lue (GET) puis modifiée (PATCH {"externalIdentifier": null}) sur la route de son
     * type (fixe ou ouverte).
     *
     * @throws AgoraLearningClientException si la session n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function detachSession(ApiObjectIdentifier $identifier): void
    {
        if ($this->getSession($identifier)->sessionData instanceof OpenedSessionOutput) {
            $this->patchSession($identifier, (new OpenedSessionPatchInput())->clearExternalIdentifier());
            return;
        }
        $this->patchSession($identifier, (new FixedSessionPatchInput())->clearExternalIdentifier());
    }

    /**
     * Supprime la session, fixe ou ouverte (DELETE, réponse 204 sans contenu).
     *
     * @throws AgoraLearningClientException si la session n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteSession(ApiObjectIdentifier $identifier): void
    {
        $this->sendDelete(ApiUrls::getDeleteSession($identifier->getId()));
    }

    /**
     * Corps commun à la création (POST) et au remplacement (PUT).
     *
     * @return array<string, mixed>
     */
    private function buildBody(SessionInput $sessionInput): array
    {
        $data = [
            'externalIdentifier' => $sessionInput->externalIdentifier->toArray(),
            'title' => $sessionInput->title,
            'description' => $sessionInput->description,
            'price' => $sessionInput->price,
            'maxPlaces' => $sessionInput->maxPlaces,
            'duration' => $sessionInput->durationInSeconds,
            'manualDuration' => $sessionInput->manualDuration,
            'image' => $sessionInput->image,
            'hasForum' => $sessionInput->hasForum,
            'notifyMailForum' => $sessionInput->notifyMailForum,
            'pedagogicalTrainer' => $sessionInput->pedagogicalTrainer,
            'administrativePerson' => $sessionInput->administrativePerson,
            'urlPreTrainingSurvey' => $sessionInput->urlPreTrainingSurvey,
            'urlOnTheSpotSurvey' => $sessionInput->urlOnTheSpotSurvey,
            'urlDelayedSurvey' => $sessionInput->urlDelayedSurvey,
        ];

        if ($sessionInput->sessionData instanceof FixedSessionInput) {
            $specificData = [
                'availabilityStartDate' => $sessionInput->sessionData->availabilityStartDate->format(self::DATETIME_FORMAT),
                'availabilityEndDate' => $sessionInput->sessionData->availabilityEndDate->format(self::DATETIME_FORMAT),
                'type' => $sessionInput->sessionData->type,
                'mode' => $sessionInput->sessionData->mode,
            ];
        } elseif ($sessionInput->sessionData instanceof OpenedSessionInput) {
            $specificData = [
                'subscribeStartDate' => $sessionInput->sessionData->subscribeStartDate->format(self::DATETIME_FORMAT),
                'subscribeEndDate' => $sessionInput->sessionData->subscribeEndDate->format(self::DATETIME_FORMAT),
                'accessDurationDays' => $sessionInput->sessionData->accessDurationDays,
                'type' => SessionType::SESSION_TYPE_INTER,
                'mode' => SessionMode::SESSION_MODE_E_LEARNING,
            ];
        } else {
            throw new \ErrorException('Unsupported session data type: ' . get_class($sessionInput->sessionData));
        }

        return array_merge($data, $specificData);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): SessionOutput
    {
        /** @var array{
         *     agoraId: string,
         *     externalIdentifier?: array<string, mixed>|null,
         *     title: string,
         *     type: string,
         *     mode: string,
         *     manualDuration: bool,
         *     hasForum: bool,
         *     notifyMailForum: bool,
         *     price: float,
         *     duration?: int|null,
         *     description?: string|null,
         *     maxPlaces?: int|null,
         *     pedagogicalTrainer?: array<string, mixed>|null,
         *     administrativePerson?: array<string, mixed>|null,
         *     urlPreTrainingSurvey?: string|null,
         *     urlOnTheSpotSurvey?: string|null,
         *     urlDelayedSurvey?: string|null,
         *     image?: string|null,
         *     dataSessionFixed?: array{availabilityStartDate: string, availabilityEndDate: string},
         *     dataSessionOpened?: array{subscribeStartDate: string, subscribeEndDate: string, accessDurationDays: int}
         * } $data */
        $duration = $data['duration'] ?? null;
        $description = $data['description'] ?? null;
        $maxPlaces = $data['maxPlaces'] ?? null;
        $urlPreTrainingSurvey = $data['urlPreTrainingSurvey'] ?? null;
        $urlOnTheSpotSurvey = $data['urlOnTheSpotSurvey'] ?? null;
        $urlDelayedSurvey = $data['urlDelayedSurvey'] ?? null;
        $externalIdentifier = !empty($data['externalIdentifier']) ? ExternalId::fromArray($data['externalIdentifier']) : null;

        $pedagogicalTrainer = isset($data['pedagogicalTrainer'])
            ? Trainer::convertDataToDTO($data['pedagogicalTrainer'])
            : null;
        $administrativePerson = isset($data['administrativePerson'])
            ? Person::convertDataToDTO($data['administrativePerson'])
            : null;

        if (array_key_exists('dataSessionFixed', $data)) {
            $openedData = $data['dataSessionFixed'];
            $sessionData = new FixedSessionOutput(
                new \DateTimeImmutable($openedData['availabilityStartDate']),
                new \DateTimeImmutable($openedData['availabilityEndDate']),
            );
        } elseif (array_key_exists('dataSessionOpened', $data)) {
            $openedData = $data['dataSessionOpened'];
            $sessionData = new OpenedSessionOutput(
                new \DateTimeImmutable($openedData['subscribeStartDate']),
                new \DateTimeImmutable($openedData['subscribeEndDate']),
                $openedData['accessDurationDays'],
            );
        } else {
            throw new \UnexpectedValueException('Session data must contain dataSessionFixed or dataSessionOpened');
        }

        return new SessionOutput(
            $data['agoraId'],
            $data['title'],
            $data['type'],
            $data['mode'],
            $sessionData,
            $data['manualDuration'],
            $data['hasForum'],
            $data['notifyMailForum'],
            $data['price'],
            $duration,
            $description,
            $maxPlaces,
            $pedagogicalTrainer,
            $administrativePerson,
            $urlPreTrainingSurvey,
            $urlOnTheSpotSurvey,
            $urlDelayedSurvey,
            $externalIdentifier,
        );
    }
}
