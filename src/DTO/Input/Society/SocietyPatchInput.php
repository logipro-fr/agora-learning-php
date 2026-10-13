<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Society;

use AgoraLearningPhp\DTO\Input\AbstractPatchInput;
use AgoraLearningPhp\Identifier\ExternalId;

/**
 * Modification partielle d'une société : seuls les champs renseignés sont envoyés.
 * Attention : pour les sociétés, l'identifiant externe est envoyé sous la clé "externalId".
 */
final class SocietyPatchInput extends AbstractPatchInput
{
    public function setExternalIdentifier(ExternalId $externalIdentifier): self
    {
        return $this->set('externalId', $externalIdentifier->toArray());
    }

    public function setName(string $name): self
    {
        return $this->set('name', $name);
    }

    public function setSiret(?string $siret): self
    {
        return $this->set('siret', $siret);
    }

    public function setLegalStatus(?string $legalStatus): self
    {
        return $this->set('legalStatus', $legalStatus);
    }

    public function setEmail(?string $email): self
    {
        return $this->set('email', $email);
    }

    public function setTelephone(?string $telephone): self
    {
        return $this->set('telephone', $telephone);
    }

    public function setAddressStreetHeadOffice(?string $addressStreetHeadOffice): self
    {
        return $this->set('addressStreetHeadOffice', $addressStreetHeadOffice);
    }

    public function setAddressPostcodeHeadOffice(?string $addressPostcodeHeadOffice): self
    {
        return $this->set('addressPostcodeHeadOffice', $addressPostcodeHeadOffice);
    }

    public function setAddressLocalityHeadOffice(?string $addressLocalityHeadOffice): self
    {
        return $this->set('addressLocalityHeadOffice', $addressLocalityHeadOffice);
    }

    public function setAddressCountryHeadOffice(?string $addressCountryHeadOffice): self
    {
        return $this->set('addressCountryHeadOffice', $addressCountryHeadOffice);
    }

    public function setAddressStreetInvoicing(?string $addressStreetInvoicing): self
    {
        return $this->set('addressStreetInvoicing', $addressStreetInvoicing);
    }

    public function setAddressPostcodeInvoicing(?string $addressPostcodeInvoicing): self
    {
        return $this->set('addressPostcodeInvoicing', $addressPostcodeInvoicing);
    }

    public function setAddressLocalityInvoicing(?string $addressLocalityInvoicing): self
    {
        return $this->set('addressLocalityInvoicing', $addressLocalityInvoicing);
    }

    public function setAddressCountryInvoicing(?string $addressCountryInvoicing): self
    {
        return $this->set('addressCountryInvoicing', $addressCountryInvoicing);
    }

    /**
     * @param string|null $legalPersonId Identifiant du contact (Agora ou "source:id"), null pour retirer la relation
     */
    public function setLegalPersonId(?string $legalPersonId): self
    {
        return $this->set('legalPersonId', $legalPersonId);
    }

    /**
     * @param string|null $administrativePersonId Identifiant du contact (Agora ou "source:id"), null pour retirer la relation
     */
    public function setAdministrativePersonId(?string $administrativePersonId): self
    {
        return $this->set('administrativePersonId', $administrativePersonId);
    }

    /**
     * @param string|null $rhPersonId Identifiant du contact (Agora ou "source:id"), null pour retirer la relation
     */
    public function setRhPersonId(?string $rhPersonId): self
    {
        return $this->set('rhPersonId', $rhPersonId);
    }

    /**
     * URL publique du logo : l'API ne supprime pas l'image existante si la valeur est vide.
     */
    public function setImage(?string $image): self
    {
        return $this->set('image', $image);
    }
}
