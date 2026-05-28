<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Society;

use AgoraLearningPhp\DTO\Input\Person\PersonInput;

final class SocietyInput
{
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
    public ?PersonInput $legalPerson;
    public ?PersonInput $administrativePerson;
    public ?PersonInput $rhPerson;
    public ?string $image;

    public function __construct(
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
        ?PersonInput $legalPerson = null,
        ?PersonInput $administrativePerson = null,
        ?PersonInput $rhPerson = null,
        ?string $image = null
    ) {
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
        $this->legalPerson = $legalPerson;
        $this->administrativePerson = $administrativePerson;
        $this->rhPerson = $rhPerson;
        $this->image = $image;
    }
}
