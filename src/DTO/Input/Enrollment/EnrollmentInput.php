<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Enrollment;

final class EnrollmentInput
{
    public string $sessionUuid;
    public string $learnerUuid;
    public bool $sendingInvite;

    public function __construct(
        string $sessionUuid,
        string $learnerUuid,
        bool $sendingInvite
    ) {
        $this->sessionUuid = $sessionUuid;
        $this->learnerUuid = $learnerUuid;
        $this->sendingInvite = $sendingInvite;
    }
}
