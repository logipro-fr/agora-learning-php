<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Program;

/**
 * Type de programme au format texte.
 */
final class ProgramText
{
    public string $programText;

    public function __construct(string $programText = '')
    {
        $this->programText = $programText;
    }

    /**
     * @return array{programText: string}
     */
    public function toArray(): array
    {
        return ['programText' => $this->programText];
    }
}
