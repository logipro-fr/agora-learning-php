<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Input\Person;

use AgoraLearningPhp\Enum\Gender;

final class PersonInput
{
    public string $familyName;
    public string $givenName;
    public ?string $email;
    public ?string $telephone;
    public ?string $addressStreet;
    public ?string $addressPostcode;
    public ?string $addressLocality;
    public ?string $addressCountry;
    public ?string $image;
    public string $gender;
    public ?\DateTimeImmutable $birthDate;
    public ?string $jobTitle;

    public function __construct(
        string $familyName,
        string $givenName,
        string $gender = Gender::GENDER_NA,
        ?string $email = null,
        ?string $telephone = null,
        ?string $addressStreet = null,
        ?string $addressPostcode = null,
        ?string $addressLocality = null,
        ?string $addressCountry = null,
        ?string $image = null,
        ?\DateTimeImmutable $birthDate = null,
        ?string $jobTitle = null
    ) {
        $this->familyName = $familyName;
        $this->givenName = $givenName;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->addressStreet = $addressStreet;
        $this->addressPostcode = $addressPostcode;
        $this->addressLocality = $addressLocality;
        $this->addressCountry = $addressCountry;
        $this->image = $image;
        $this->gender = $gender;
        $this->birthDate = $birthDate;
        $this->jobTitle = $jobTitle;
    }
}
