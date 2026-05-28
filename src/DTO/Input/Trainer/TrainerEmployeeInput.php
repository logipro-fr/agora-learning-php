<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Trainer;

use AgoraLearningPhp\Enum\Gender;

final class TrainerEmployeeInput
{
    public string $familyName;
    public string $givenName;
    public string $email;
    public bool $visibleInformation;
    public bool $notifyUser;
    public string $gender;
    public ?string $telephone;
    public ?string $mobileNumber;
    public ?string $addressStreet;
    public ?string $addressPostcode;
    public ?string $addressLocality;
    public ?string $addressCountry;
    public ?string $image;
    public ?\DateTimeImmutable $birthDate;
    public ?string $jobTitle;
    public ?string $societyId;
    public ?string $cv;
    public ?string $degree;
    public ?string $contract;
    public ?string $jobDescription;

    public function __construct(
        string $familyName,
        string $givenName,
        string $email,
        bool $visibleInformation = true,
        bool $notifyUser = true,
        string $gender = Gender::GENDER_NA,
        ?string $telephone = null,
        ?string $mobileNumber = null,
        ?string $addressStreet = null,
        ?string $addressPostcode = null,
        ?string $addressLocality = null,
        ?string $addressCountry = null,
        ?string $image = null,
        ?\DateTimeImmutable $birthDate = null,
        ?string $jobTitle = null,
        ?string $societyId = null,
        ?string $cv = null,
        ?string $degree = null,
        ?string $contract = null,
        ?string $jobDescription = null
    ) {
        $this->familyName = $familyName;
        $this->givenName = $givenName;
        $this->email = $email;
        $this->visibleInformation = $visibleInformation;
        $this->notifyUser = $notifyUser;
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
        $this->societyId = $societyId;
        $this->cv = $cv;
        $this->degree = $degree;
        $this->contract = $contract;
        $this->jobDescription = $jobDescription;
    }
}
