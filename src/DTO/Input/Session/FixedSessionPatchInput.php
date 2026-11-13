<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Session;

/**
 * Modification partielle d'une session à dates fixes (PATCH /sessions/fixed/{id}).
 * La cohérence des dates est vérifiée par l'API avec les valeurs déjà enregistrées.
 */
final class FixedSessionPatchInput extends AbstractSessionPatchInput
{
    /**
     * @param string $type Valeur de AgoraLearningPhp\Enum\SessionType
     */
    public function setType(string $type): self
    {
        return $this->set('type', $type);
    }

    /**
     * @param string $mode Valeur de AgoraLearningPhp\Enum\SessionMode
     */
    public function setMode(string $mode): self
    {
        return $this->set('mode', $mode);
    }

    public function setAvailabilityStartDate(\DateTimeInterface $availabilityStartDate): self
    {
        return $this->setDate('availabilityStartDate', $availabilityStartDate);
    }

    public function setAvailabilityEndDate(\DateTimeInterface $availabilityEndDate): self
    {
        return $this->setDate('availabilityEndDate', $availabilityEndDate);
    }
}
