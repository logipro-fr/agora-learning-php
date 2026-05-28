<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Trainer;

use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\Enum\Gender;

final class TrainerFreeOutput extends TrainerOutput
{
    public string $uuid;
    public string $familyName;
    public string $givenName;
    public string $email;
    public bool $visibleInformation;
    public string $gender;
    public array $educationalManagerSessions;
    public ?string $telephone;
    public ?string $mobileNumber;
    public ?string $addressStreet;
    public ?string $addressPostcode;
    public ?string $addressLocality;
    public ?string $addressCountry;
    public ?string $image;
    public ?\DateTimeImmutable $birthDate;
    public ?string $jobTitle;
    public ?SocietyOutput $society;
    public ?string $cv;
    public ?string $degree;
    public ?string $contract;
    public ?string $jobDescription;
    public ?float $hourlyCost;
    public ?float $daylyCost;
    public ?string $siret;
    public ?string $tvaNumber;
    public ?string $urssafNumber;
    public ?string $urssafCertificate;
    public ?string $billingAddressStreet;
    public ?string $billingAddressPostcode;
    public ?string $billingAddressLocality;
    public ?string $billingAddressCountry;

    public function __construct(
        string $uuid,
        string $familyName,
        string $givenName,
        string $email,
        bool $visibleInformation = true,
        string $gender = Gender::GENDER_NA,
        array $educationalManagerSessions = [],
        ?string $telephone = null,
        ?string $mobileNumber = null,
        ?string $addressStreet = null,
        ?string $addressPostcode = null,
        ?string $addressLocality = null,
        ?string $addressCountry = null,
        ?string $image = null,
        ?\DateTimeImmutable $birthDate = null,
        ?string $jobTitle = null,
        ?SocietyOutput $society = null,
        ?string $cv = null,
        ?string $degree = null,
        ?string $contract = null,
        ?string $jobDescription = null,
        ?float $hourlyCost = null,
        ?float $daylyCost = null,
        ?string $siret = null,
        ?string $tvaNumber = null,
        ?string $urssafNumber = null,
        ?string $urssafCertificate = null,
        ?string $billingAddressStreet = null,
        ?string $billingAddressPostcode = null,
        ?string $billingAddressLocality = null,
        ?string $billingAddressCountry = null
    ) {
        $this->uuid = $uuid;
        $this->familyName = $familyName;
        $this->givenName = $givenName;
        $this->email = $email;
        $this->visibleInformation = $visibleInformation;
        $this->gender = $gender;
        $this->telephone = $telephone;
        $this->mobileNumber = $mobileNumber;
        $this->addressStreet = $addressStreet;
        $this->addressPostcode = $addressPostcode;
        $this->addressLocality = $addressLocality;
        $this->addressCountry = $addressCountry;
        $this->image = $image;
        $this->birthDate = $birthDate;
        $this->jobTitle = $jobTitle;
        $this->society = $society;
        $this->cv = $cv;
        $this->degree = $degree;
        $this->contract = $contract;
        $this->jobDescription = $jobDescription;
        $this->hourlyCost = $hourlyCost;
        $this->daylyCost = $daylyCost;
        $this->siret = $siret;
        $this->tvaNumber = $tvaNumber;
        $this->urssafNumber = $urssafNumber;
        $this->urssafCertificate = $urssafCertificate;
        $this->billingAddressStreet = $billingAddressStreet;
        $this->billingAddressPostcode = $billingAddressPostcode;
        $this->billingAddressLocality = $billingAddressLocality;
        $this->billingAddressCountry = $billingAddressCountry;
        $this->educationalManagerSessions = $educationalManagerSessions;
    }
}
