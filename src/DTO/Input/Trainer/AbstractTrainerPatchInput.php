<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Trainer;

use AgoraLearningPhp\DTO\Input\AbstractContactPatchInput;

/**
 * Champs communs aux modifications partielles des formateurs (employé et indépendant).
 * notifyUser n'existe pas en PATCH : il n'est pris en compte qu'à la création.
 * Les fichiers (cv, degree, contract, jobDescription) sont des URL publiques : une valeur vide
 * ne supprime pas le fichier existant.
 */
abstract class AbstractTrainerPatchInput extends AbstractContactPatchInput
{
    /**
     * @return static
     */
    public function setVisibleInformation(bool $visibleInformation): self
    {
        return $this->set('visibleInformation', $visibleInformation);
    }

    /**
     * @return static
     */
    public function setCv(?string $cv): self
    {
        return $this->set('cv', $cv);
    }

    /**
     * @return static
     */
    public function setDegree(?string $degree): self
    {
        return $this->set('degree', $degree);
    }

    /**
     * @return static
     */
    public function setContract(?string $contract): self
    {
        return $this->set('contract', $contract);
    }

    /**
     * @return static
     */
    public function setJobDescription(?string $jobDescription): self
    {
        return $this->set('jobDescription', $jobDescription);
    }
}
