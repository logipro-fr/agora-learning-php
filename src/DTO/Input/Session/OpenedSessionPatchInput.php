<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Session;

/**
 * Modification partielle d'une session ouverte (PATCH /sessions/opened/{id}).
 * Une session ouverte reste toujours INTER / E_LEARNING.
 * La cohérence des dates est vérifiée par l'API avec les valeurs déjà enregistrées.
 */
final class OpenedSessionPatchInput extends AbstractSessionPatchInput
{
    public function setSubscribeStartDate(\DateTimeInterface $subscribeStartDate): self
    {
        return $this->setDate('subscribeStartDate', $subscribeStartDate);
    }

    public function setSubscribeEndDate(\DateTimeInterface $subscribeEndDate): self
    {
        return $this->setDate('subscribeEndDate', $subscribeEndDate);
    }

    public function setAccessDurationDays(int $accessDurationDays): self
    {
        return $this->set('accessDurationDays', $accessDurationDays);
    }
}
