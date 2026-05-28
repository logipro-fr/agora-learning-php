<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Session;

final class OpenedSessionInput implements SpecificSessionInputInterface
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
