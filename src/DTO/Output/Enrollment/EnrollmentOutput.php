<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Enrollment;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;

class EnrollmentOutput extends ApiOutput
{
    public string $uuid;
    public LearnerOutput $learner;
    public SessionOutput $session;
    public ?\DateTimeImmutable $availabilityStartDate;
    public ?\DateTimeImmutable $availabilityEndDate;

    public function __construct(
        string $uuid,
        LearnerOutput $learner,
        SessionOutput $session,
        ?\DateTimeImmutable $availabilityStartDate = null,
        ?\DateTimeImmutable $availabilityEndDate = null
    ) {
        $this->uuid = $uuid;
        $this->learner = $learner;
        $this->session = $session;
        $this->availabilityStartDate = $availabilityStartDate;
        $this->availabilityEndDate = $availabilityEndDate;
    }
}
