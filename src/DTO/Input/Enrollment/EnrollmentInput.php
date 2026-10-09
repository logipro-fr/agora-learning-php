<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Enrollment;

use AgoraLearningPhp\Identifier\ExternalId;

final class EnrollmentInput
{
    public ExternalId $externalIdentifier;
    public string $sessionUuid;
    public string $learnerUuid;
    public bool $sendingInvite;

    public function __construct(
        ExternalId $externalIdentifier,
        string $sessionUuid,
        string $learnerUuid,
        bool $sendingInvite
    ) {
        $this->externalIdentifier = $externalIdentifier;
        $this->sessionUuid = $sessionUuid;
        $this->learnerUuid = $learnerUuid;
        $this->sendingInvite = $sendingInvite;
    }
}
