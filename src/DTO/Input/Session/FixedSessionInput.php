<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Session;

final class FixedSessionInput implements SpecificSessionInputInterface
{
    public \DateTimeImmutable $availabilityStartDate;
    public \DateTimeImmutable $availabilityEndDate;
    public string $type;
    public string $mode;

    public function __construct(
        \DateTimeImmutable $availabilityStartDate,
        \DateTimeImmutable $availabilityEndDate,
        string $type,
        string $mode
    ) {
        $this->availabilityStartDate = $availabilityStartDate;
        $this->availabilityEndDate = $availabilityEndDate;
        $this->type = $type;
        $this->mode = $mode;
    }
}
