<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Session;

use AgoraLearningPhp\DTO\Output\ApiOutput;

class OpenedSessionOutput extends ApiOutput implements SpecificSessionOutputInterface
{
    public \DateTimeImmutable $subscribeStartDate;
    public \DateTimeImmutable $subscribeEndDate;
    public int $accessDurationDays;

    public function __construct(
        \DateTimeImmutable $subscribeStartDate,
        \DateTimeImmutable $subscribeEndDate,
        int $accessDurationDays
    ) {
        $this->subscribeStartDate = $subscribeStartDate;
        $this->subscribeEndDate = $subscribeEndDate;
        $this->accessDurationDays = $accessDurationDays;
    }
}
