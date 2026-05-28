<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Enrollment;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;

class EnrollmentOutput extends ApiOutput
{
    public string $uuid;
    public PersonOutput $learner;
    public SessionOutput $session;
    public ?\DateTimeImmutable $availabilityStartDate;
    public ?\DateTimeImmutable $availabilityEndDate;

    public function __construct(
        string $uuid,
        PersonOutput $learner,
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
