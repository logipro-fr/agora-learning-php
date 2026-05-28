<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Session;

use AgoraLearningPhp\DTO\Output\ApiOutput;

class FixedSessionOutput extends ApiOutput implements SpecificSessionOutputInterface
{
    public \DateTimeImmutable $availabilityStartDate;
    public \DateTimeImmutable $availabilityEndDate;

    public function __construct(
        \DateTimeImmutable $availabilityStartDate,
        \DateTimeImmutable $availabilityEndDate
    ) {
        $this->availabilityStartDate = $availabilityStartDate;
        $this->availabilityEndDate = $availabilityEndDate;
    }
}
