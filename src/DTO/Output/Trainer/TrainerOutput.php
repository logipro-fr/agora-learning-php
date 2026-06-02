<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Trainer;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Society\SocietyOutput;
use AgoraLearningPhp\Enum\Gender;

class TrainerOutput extends ApiOutput
{
    public string $uuid;
    public string $familyName;
    public string $givenName;
    public string $email;
    public bool $visibleInformation;
    public string $gender;
    /** @var array<int, mixed> */
    public array $educationalManagerSessions;
    public ?string $telephone;
    public ?string $mobileNumber;
    public ?string $addressStreet;
    public ?string $addressPostcode;
    public ?string $addressLocality;
    public ?string $addressCountry;
    public ?\DateTimeImmutable $birthDate;
    public ?string $jobTitle;
    public ?SocietyOutput $society;

    /**
     * @param array<int, mixed> $educationalManagerSessions
     */
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
        ?\DateTimeImmutable $birthDate = null,
        ?string $jobTitle = null,
        ?SocietyOutput $society = null
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
        $this->birthDate = $birthDate;
        $this->jobTitle = $jobTitle;
        $this->society = $society;
        $this->educationalManagerSessions = $educationalManagerSessions;
    }
}
