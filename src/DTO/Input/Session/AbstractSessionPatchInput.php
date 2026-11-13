<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Session;

use AgoraLearningPhp\DTO\Input\AbstractPatchInput;
use AgoraLearningPhp\Identifier\ExternalId;

/**
 * Champs communs aux modifications partielles des sessions (à dates fixes et ouvertes).
 * Le type de session (fixe / ouverte) ne peut pas être changé par l'API.
 */
abstract class AbstractSessionPatchInput extends AbstractPatchInput
{
    /**
     * @return static
     */
    public function setExternalIdentifier(ExternalId $externalIdentifier): self
    {
        return $this->set('externalIdentifier', $externalIdentifier->toArray());
    }

    /**
     * @return static
     */
    public function setTitle(string $title): self
    {
        return $this->set('title', $title);
    }

    /**
     * @return static
     */
    public function setDescription(?string $description): self
    {
        return $this->set('description', $description);
    }

    /**
     * @return static
     */
    public function setDurationInSeconds(int $durationInSeconds): self
    {
        return $this->set('duration', $durationInSeconds);
    }

    /**
     * @return static
     */
    public function setManualDuration(bool $manualDuration): self
    {
        return $this->set('manualDuration', $manualDuration);
    }

    /**
     * @return static
     */
    public function setPrice(float $price): self
    {
        return $this->set('price', $price);
    }

    /**
     * @param int|null $maxPlaces null = nombre de places illimité
     * @return static
     */
    public function setMaxPlaces(?int $maxPlaces): self
    {
        return $this->set('maxPlaces', $maxPlaces);
    }

    /**
     * @return static
     */
    public function setHasForum(bool $hasForum): self
    {
        return $this->set('hasForum', $hasForum);
    }

    /**
     * @return static
     */
    public function setNotifyMailForum(bool $notifyMailForum): self
    {
        return $this->set('notifyMailForum', $notifyMailForum);
    }

    /**
     * @param string|null $pedagogicalTrainer Identifiant du formateur (Agora ou "source:id"), null pour retirer la relation
     * @return static
     */
    public function setPedagogicalTrainer(?string $pedagogicalTrainer): self
    {
        return $this->set('pedagogicalTrainer', $pedagogicalTrainer);
    }

    /**
     * @param string|null $administrativePerson Identifiant du contact (Agora ou "source:id"), null pour retirer la relation
     * @return static
     */
    public function setAdministrativePerson(?string $administrativePerson): self
    {
        return $this->set('administrativePerson', $administrativePerson);
    }

    /**
     * @return static
     */
    public function setUrlPreTrainingSurvey(?string $urlPreTrainingSurvey): self
    {
        return $this->set('urlPreTrainingSurvey', $urlPreTrainingSurvey);
    }

    /**
     * @return static
     */
    public function setUrlOnTheSpotSurvey(?string $urlOnTheSpotSurvey): self
    {
        return $this->set('urlOnTheSpotSurvey', $urlOnTheSpotSurvey);
    }

    /**
     * @return static
     */
    public function setUrlDelayedSurvey(?string $urlDelayedSurvey): self
    {
        return $this->set('urlDelayedSurvey', $urlDelayedSurvey);
    }

    /**
     * URL publique de l'illustration : l'API ne supprime pas l'image existante si la valeur est vide.
     *
     * @return static
     */
    public function setImage(?string $image): self
    {
        return $this->set('image', $image);
    }
}
