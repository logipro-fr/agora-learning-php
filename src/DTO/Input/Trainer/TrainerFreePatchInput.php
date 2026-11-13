<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Trainer;

/**
 * Modification partielle d'un formateur indépendant : seuls les champs renseignés sont envoyés.
 */
final class TrainerFreePatchInput extends AbstractTrainerPatchInput
{
    /**
     * @return static
     */
    public function setHourlyCost(?float $hourlyCost): self
    {
        return $this->set('hourlyCost', $hourlyCost);
    }

    /**
     * @return static
     */
    public function setDaylyCost(?float $daylyCost): self
    {
        return $this->set('daylyCost', $daylyCost);
    }

    /**
     * @return static
     */
    public function setSiret(?string $siret): self
    {
        return $this->set('siret', $siret);
    }

    /**
     * @return static
     */
    public function setTvaNumber(?string $tvaNumber): self
    {
        return $this->set('tvaNumber', $tvaNumber);
    }

    /**
     * @return static
     */
    public function setUrssafNumber(?string $urssafNumber): self
    {
        return $this->set('urssafNumber', $urssafNumber);
    }

    /**
     * @return static
     */
    public function setUrssafCertificate(?string $urssafCertificate): self
    {
        return $this->set('urssafCertificate', $urssafCertificate);
    }

    /**
     * @return static
     */
    public function setBillingAddressStreet(?string $billingAddressStreet): self
    {
        return $this->set('billingAddressStreet', $billingAddressStreet);
    }

    /**
     * @return static
     */
    public function setBillingAddressPostcode(?string $billingAddressPostcode): self
    {
        return $this->set('billingAddressPostcode', $billingAddressPostcode);
    }

    /**
     * @return static
     */
    public function setBillingAddressLocality(?string $billingAddressLocality): self
    {
        return $this->set('billingAddressLocality', $billingAddressLocality);
    }

    /**
     * @return static
     */
    public function setBillingAddressCountry(?string $billingAddressCountry): self
    {
        return $this->set('billingAddressCountry', $billingAddressCountry);
    }
}
