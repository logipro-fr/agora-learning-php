<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Program;

/**
 * Type de programme au format liste à deux niveaux, avec une description : chaque élément principal est
 * une ProgramComplexList (description de l'élément et ses sous-éléments).
 */
final class ProgramBiLevelComplexList
{
    public ?string $description;
    /** @var ProgramComplexList[] */
    public array $programComplexList;

    /**
     * @param ProgramComplexList[] $programComplexList
     */
    public function __construct(?string $description = null, array $programComplexList = [])
    {
        $this->description = $description;
        $this->programComplexList = $programComplexList;
    }

    /**
     * @return array{
     *     description: string|null,
     *     programComplexList: array<int, array{
     *         description: string|null,
     *         programSimpleList: array{programSimpleList: string[]}
     *     }>
     * }
     */
    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'programComplexList' => array_map(
                static function (ProgramComplexList $element): array {
                    return $element->toArray();
                },
                array_values($this->programComplexList)
            ),
        ];
    }
}
