<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input;

use AgoraLearningPhp\Service\ClientCore\ApiService;

/**
 * Base des modifications partielles (PATCH, application/merge-patch+json).
 *
 * Seuls les champs renseignés via un setter sont envoyés :
 * - un champ jamais renseigné n'est pas modifié par l'API ;
 * - un champ renseigné à null est vidé (les champs obligatoires n'acceptent pas null).
 */
abstract class AbstractPatchInput
{
    /**
     * @var array<string, mixed>
     */
    private array $fields = [];

    /**
     * Champs à envoyer, tels qu'attendus dans le corps de la requête.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->fields;
    }

    public function isEmpty(): bool
    {
        return $this->fields === [];
    }

    /**
     * @param mixed $value
     * @return static
     */
    protected function set(string $field, $value): self
    {
        $this->fields[$field] = $value;
        return $this;
    }

    /**
     * @return static
     */
    protected function setDate(string $field, ?\DateTimeInterface $date): self
    {
        return $this->set($field, $date !== null ? $date->format(ApiService::DATETIME_FORMAT) : null);
    }
}
