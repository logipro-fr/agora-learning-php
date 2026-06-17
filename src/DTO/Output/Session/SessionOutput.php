<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Session;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Trainer\TrainerOutput;

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
    public ?int $duration;
    public ?string $description;
    public ?int $maxPlaces;
    public ?TrainerOutput $pedagogicalTrainer;
    public ?PersonOutput $administrativePerson;
    public ?string $urlPreTrainingSurvey;
    public ?string $urlOnTheSpotSurvey;
    public ?string $urlDelayedSurvey;

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
        ?int $duration = null,
        ?string $description = null,
        ?int $maxPlaces = null,
        ?TrainerOutput $pedagogicalTrainer = null,
        ?PersonOutput $administrativePerson = null,
        ?string $urlPreTrainingSurvey = null,
        ?string $urlOnTheSpotSurvey = null,
        ?string $urlDelayedSurvey = null
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
        $this->pedagogicalTrainer = $pedagogicalTrainer;
        $this->administrativePerson = $administrativePerson;
        $this->urlPreTrainingSurvey = $urlPreTrainingSurvey;
        $this->urlOnTheSpotSurvey = $urlOnTheSpotSurvey;
        $this->urlDelayedSurvey = $urlDelayedSurvey;
    }
}
