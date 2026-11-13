<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Society;

use AgoraLearningPhp\Identifier\ExternalId;

/**
 * Les responsables (légal, administratif, RH) sont des contacts existants, désignés par leur identifiant
 * (Agora ou "source:id"). En remplacement (PUT), un identifiant absent retire la relation.
 */
final class SocietyInput
{
    public ExternalId $externalIdentifier;
    public string $name;
    public ?string $siret;
    public ?string $legalStatus;
    public ?string $email;
    public ?string $telephone;
    public ?string $addressStreetHeadOffice;
    public ?string $addressPostcodeHeadOffice;
    public ?string $addressLocalityHeadOffice;
    public ?string $addressCountryHeadOffice;
    public ?string $addressStreetInvoicing;
    public ?string $addressPostcodeInvoicing;
    public ?string $addressLocalityInvoicing;
    public ?string $addressCountryInvoicing;
    public ?string $legalPersonId;
    public ?string $administrativePersonId;
    public ?string $rhPersonId;
    public ?string $image;

    public function __construct(
        ExternalId $externalIdentifier,
        string $name,
        ?string $siret = null,
        ?string $legalStatus = null,
        ?string $email = null,
        ?string $telephone = null,
        ?string $addressStreetHeadOffice = null,
        ?string $addressPostcodeHeadOffice = null,
        ?string $addressLocalityHeadOffice = null,
        ?string $addressCountryHeadOffice = null,
        ?string $addressStreetInvoicing = null,
        ?string $addressPostcodeInvoicing = null,
        ?string $addressLocalityInvoicing = null,
        ?string $addressCountryInvoicing = null,
        ?string $legalPersonId = null,
        ?string $administrativePersonId = null,
        ?string $rhPersonId = null,
        ?string $image = null
    ) {
        $this->externalIdentifier = $externalIdentifier;
        $this->name = $name;
        $this->siret = $siret;
        $this->legalStatus = $legalStatus;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->addressStreetHeadOffice = $addressStreetHeadOffice;
        $this->addressPostcodeHeadOffice = $addressPostcodeHeadOffice;
        $this->addressLocalityHeadOffice = $addressLocalityHeadOffice;
        $this->addressCountryHeadOffice = $addressCountryHeadOffice;
        $this->addressStreetInvoicing = $addressStreetInvoicing;
        $this->addressPostcodeInvoicing = $addressPostcodeInvoicing;
        $this->addressLocalityInvoicing = $addressLocalityInvoicing;
        $this->addressCountryInvoicing = $addressCountryInvoicing;
        $this->legalPersonId = $legalPersonId;
        $this->administrativePersonId = $administrativePersonId;
        $this->rhPersonId = $rhPersonId;
        $this->image = $image;
    }
}
