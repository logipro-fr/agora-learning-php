<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Program;

/**
 * Type de programme au format liste de textes, avec une description.
 * Sert aussi d'élément principal (description) et de ses sous-éléments dans ProgramBiLevelComplexList.
 */
final class ProgramComplexList
{
    public ?string $description;
    public ProgramSimpleList $programSimpleList;

    public function __construct(?string $description = null, ?ProgramSimpleList $programSimpleList = null)
    {
        $this->description = $description;
        $this->programSimpleList = $programSimpleList ?? new ProgramSimpleList();
    }

    /**
     * @return array{description: string|null, programSimpleList: array{programSimpleList: string[]}}
     */
    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'programSimpleList' => $this->programSimpleList->toArray(),
        ];
    }
}
