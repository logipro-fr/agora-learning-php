<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Enrollment;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\Identifier\ExternalId;

class EnrollmentOutput extends ApiOutput
{
    public string $uuid;
    public LearnerOutput $learner;
    public SessionOutput $session;
    public ?\DateTimeImmutable $availabilityStartDate;
    public ?\DateTimeImmutable $availabilityEndDate;
    public ?ExternalId $externalIdentifier;

    public function __construct(
        string $uuid,
        LearnerOutput $learner,
        SessionOutput $session,
        ?\DateTimeImmutable $availabilityStartDate = null,
        ?\DateTimeImmutable $availabilityEndDate = null,
        ?ExternalId $externalIdentifier = null
    ) {
        $this->uuid = $uuid;
        $this->learner = $learner;
        $this->session = $session;
        $this->availabilityStartDate = $availabilityStartDate;
        $this->availabilityEndDate = $availabilityEndDate;
        $this->externalIdentifier = $externalIdentifier;
    }
}
