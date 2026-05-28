<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Session;

final class SessionInput
{
    public string $title;
    public SpecificSessionInputInterface $sessionData;
    public bool $manualDuration = false;
    public bool $hasForum = true;
    public bool $notifyMailForum = true;
    public float $price = 0;
    public int $durationInSeconds;
    public ?string $description;
    public ?int $maxPlaces;
    public ?string $urlPreTrainingSurvey;
    public ?string $urlOnTheSpotSurvey;
    public ?string $urlDelayedSurvey;
    public ?string $image;

    public function __construct(
        string $title,
        SpecificSessionInputInterface $sessionData,
        bool $manualDuration = false,
        bool $hasForum = true,
        bool $notifyMailForum = true,
        float $price = 0,
        int $durationInSeconds = 0,
        ?string $description = null,
        ?int $maxPlaces = null,
        ?string $urlPreTrainingSurvey = null,
        ?string $urlOnTheSpotSurvey = null,
        ?string $urlDelayedSurvey = null,
        ?string $image = null
    ) {
        $this->title = $title;
        $this->sessionData = $sessionData;
        $this->manualDuration = $manualDuration;
        $this->hasForum = $hasForum;
        $this->notifyMailForum = $notifyMailForum;
        $this->price = $price;
        $this->durationInSeconds = $durationInSeconds;
        $this->description = $description;
        $this->maxPlaces = $maxPlaces;
        $this->urlPreTrainingSurvey = $urlPreTrainingSurvey;
        $this->urlOnTheSpotSurvey = $urlOnTheSpotSurvey;
        $this->urlDelayedSurvey = $urlDelayedSurvey;
        $this->image = $image;
    }
}
