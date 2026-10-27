<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Person;

use AgoraLearningPhp\DTO\Input\AbstractContactPatchInput;

/**
 * Modification partielle d'un contact : seuls les champs renseignés sont envoyés.
 */
final class PersonPatchInput extends AbstractContactPatchInput
{
    /**
     * Retire l'identifiant externe : la personne n'est plus liée à l'application source.
     */
    public function clearExternalIdentifier(): self
    {
        return $this->set('externalIdentifier', null);
    }
}
