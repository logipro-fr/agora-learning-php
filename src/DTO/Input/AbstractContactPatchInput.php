<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input;

use AgoraLearningPhp\Identifier\ExternalId;

/**
 * Champs de contact communs aux modifications partielles de Person, Learner et Trainer.
 * Les champs obligatoires (identifiant externe, nom, prénom, e-mail, genre) ne peuvent pas être vidés.
 */
abstract class AbstractContactPatchInput extends AbstractPatchInput
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
    public function setFamilyName(string $familyName): self
    {
        return $this->set('familyName', $familyName);
    }

    /**
     * @return static
     */
    public function setGivenName(string $givenName): self
    {
        return $this->set('givenName', $givenName);
    }

    /**
     * @return static
     */
    public function setEmail(string $email): self
    {
        return $this->set('email', $email);
    }

    /**
     * @param string $gender Valeur de AgoraLearningPhp\Enum\Gender
     * @return static
     */
    public function setGender(string $gender): self
    {
        return $this->set('gender', $gender);
    }

    /**
     * @return static
     */
    public function setTelephone(?string $telephone): self
    {
        return $this->set('telephone', $telephone);
    }

    /**
     * @return static
     */
    public function setMobileNumber(?string $mobileNumber): self
    {
        return $this->set('mobileNumber', $mobileNumber);
    }

    /**
     * @return static
     */
    public function setAddressStreet(?string $addressStreet): self
    {
        return $this->set('addressStreet', $addressStreet);
    }

    /**
     * @return static
     */
    public function setAddressPostcode(?string $addressPostcode): self
    {
        return $this->set('addressPostcode', $addressPostcode);
    }

    /**
     * @return static
     */
    public function setAddressLocality(?string $addressLocality): self
    {
        return $this->set('addressLocality', $addressLocality);
    }

    /**
     * @return static
     */
    public function setAddressCountry(?string $addressCountry): self
    {
        return $this->set('addressCountry', $addressCountry);
    }

    /**
     * URL publique de l'image : l'API ne supprime pas l'image existante si la valeur est vide.
     *
     * @return static
     */
    public function setImage(?string $image): self
    {
        return $this->set('image', $image);
    }

    /**
     * @return static
     */
    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        return $this->setDate('birthDate', $birthDate);
    }

    /**
     * @return static
     */
    public function setJobTitle(?string $jobTitle): self
    {
        return $this->set('jobTitle', $jobTitle);
    }

    /**
     * @param string|null $societyId Identifiant de la société (Agora ou "source:id"), null pour retirer la relation
     * @return static
     */
    public function setSocietyId(?string $societyId): self
    {
        return $this->set('societyId', $societyId);
    }
}
