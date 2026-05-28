<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Session;

use AgoraLearningPhp\DTO\Output\ApiOutput;

class SessionOutput extends ApiOutput
{
    public string $uuid;
    public string $title;
    public string $type;
    public string $mode;
    public SpecificSessionOutputInterface $sessionData;
    public bool $manualDuration = false;
    public bool $hasForum = true;
    public bool $notifyMailForum = true;
    public float $price = 0;
    public ?string $duration;
    public ?string $description;
    public ?int $maxPlaces;
    public ?string $urlPreTrainingSurvey;
    public ?string $urlOnTheSpotSurvey;
    public ?string $urlDelayedSurvey;
    public ?string $image;

    public function __construct(
        string $uuid,
        string $title,
        string $type,
        string $mode,
        SpecificSessionOutputInterface $sessionData,
        bool $manualDuration = false,
        bool $hasForum = true,
        bool $notifyMailForum = true,
        float $price = 0,
        ?string $duration = null,
        ?string $description = null,
        ?int $maxPlaces = null,
        ?string $urlPreTrainingSurvey = null,
        ?string $urlOnTheSpotSurvey = null,
        ?string $urlDelayedSurvey = null,
        ?string $image = null
    ) {
        $this->uuid = $uuid;
        $this->title = $title;
        $this->type = $type;
        $this->mode = $mode;
        $this->sessionData = $sessionData;
        $this->manualDuration = $manualDuration;
        $this->hasForum = $hasForum;
        $this->notifyMailForum = $notifyMailForum;
        $this->price = $price;
        $this->duration = $duration;
        $this->description = $description;
        $this->maxPlaces = $maxPlaces;
        $this->urlPreTrainingSurvey = $urlPreTrainingSurvey;
        $this->urlOnTheSpotSurvey = $urlOnTheSpotSurvey;
        $this->urlDelayedSurvey = $urlDelayedSurvey;
        $this->image = $image;
    }
}
