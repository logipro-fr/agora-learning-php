<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Learner;

use AgoraLearningPhp\DTO\Input\AbstractContactPatchInput;

/**
 * Modification partielle d'un apprenant : seuls les champs renseignés sont envoyés.
 * L'identifiant de connexion et le statut du compte ne sont pas modifiables.
 */
final class LearnerPatchInput extends AbstractContactPatchInput
{
    /**
     * @return static
     */
    public function setRecoverEmail(string $recoverEmail): self
    {
        return $this->set('recoverEmail', $recoverEmail);
    }
}
