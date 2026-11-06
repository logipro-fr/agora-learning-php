<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Program;

use AgoraLearningPhp\DTO\Input\Program\ProgramInput;
use AgoraLearningPhp\DTO\Output\ApiOutput;

/**
 * Programme de formation d'une session sur Agora.
 *
 * Agora renvoie les mêmes structures qu'il reçoit : $program peut être modifié puis renvoyé tel quel à
 * replaceProgram(), ce qui permet de ne changer que certains types (un type absent du PUT est vidé).
 */
final class ProgramOutput extends ApiOutput
{
    public string $sessionAgoraId;
    public ProgramInput $program;

    public function __construct(string $sessionAgoraId, ProgramInput $program)
    {
        $this->sessionAgoraId = $sessionAgoraId;
        $this->program = $program;
    }
}
