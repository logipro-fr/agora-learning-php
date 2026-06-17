<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Session;

use AgoraLearningPhp\DTO\Input\Session\FixedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\OpenedSessionInput;
use AgoraLearningPhp\DTO\Input\Session\SessionInput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\OpenedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
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

    public function getSession(string $uuid): SessionOutput
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::GET, ApiUrls::getGetSession($uuid), $body);
        return $this->convertResponseToDTO($response);
    }

    public function postSession(SessionInput $sessionInput): SessionOutput
    {
        $data = [
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
            $url = ApiUrls::getCreateFixedSession();
            $specificData = [
                'availabilityStartDate' => $sessionInput->sessionData->availabilityStartDate->format(self::DATETIME_FORMAT),
                'availabilityEndDate' => $sessionInput->sessionData->availabilityEndDate->format(self::DATETIME_FORMAT),
                'type' => $sessionInput->sessionData->type,
                'mode' => $sessionInput->sessionData->mode,
            ];
        } elseif ($sessionInput->sessionData instanceof OpenedSessionInput) {
            $url = ApiUrls::getCreateOpenedSession();
            $specificData = [
                'subscribeStartDate' => $sessionInput->sessionData->subscribeStartDate->format(self::DATETIME_FORMAT),
                'subscribeEndDate' => $sessionInput->sessionData->subscribeEndDate->format(self::DATETIME_FORMAT),
                'accessDurationDays' => $sessionInput->sessionData->accessDurationDays,
                'type' => SessionType::SESSION_TYPE_INTER,
                'mode' => SessionMode::SESSION_MODE_E_LEARNING,
            ];
        } else {
            throw new \ErrorException();
        }

        $data = array_merge($data, $specificData);
        $body = JsonHandler::jsonEncode($data);
        $response = $this->httpClient->request(RequestMethod::POST, $url, $body);
        return $this->convertResponseToDTO($response);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): SessionOutput
    {
        /** @var array{
         *     id: string,
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

        $pedagogicalTrainer = array_key_exists('pedagogicalTrainer', $data)
            ? Trainer::convertDataToDTO($data['pedagogicalTrainer'])
            : null;
        $administrativePerson = array_key_exists('administrativePerson', $data)
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
            throw new \Exception();
        }

        return new SessionOutput(
            $data['id'],
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
            $urlDelayedSurvey
        );
    }
}
