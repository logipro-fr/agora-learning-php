<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Program;

/**
 * Type de programme au format liste de textes, sans description.
 */
final class ProgramSimpleList
{
    /** @var string[] */
    public array $programSimpleList;

    /**
     * @param string[] $programSimpleList
     */
    public function __construct(array $programSimpleList = [])
    {
        $this->programSimpleList = $programSimpleList;
    }

    /**
     * @return array{programSimpleList: string[]}
     */
    public function toArray(): array
    {
        return ['programSimpleList' => array_values($this->programSimpleList)];
    }
}
