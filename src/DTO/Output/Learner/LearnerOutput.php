<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Learner;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\Enum\Gender;
use AgoraLearningPhp\Identifier\ExternalId;

final class LearnerOutput extends ApiOutput
{
    public string $uuid;
    public string $username;
    public string $familyName;
    public string $givenName;
    public string $gender;
    public string $recoverEmail;
    public string $email;
    public ?string $telephone;
    public ?string $addressStreet;
    public ?string $addressPostcode;
    public ?string $addressLocality;
    public ?string $addressCountry;
    public ?\DateTimeImmutable $birthDate;
    public ?string $jobTitle;
    public ?ExternalId $externalIdentifier;

    public function __construct(
        string $uuid,
        string $username,
        string $familyName,
        string $givenName,
        string $recoverEmail,
        string $email,
        string $gender = Gender::GENDER_NA,
        ?string $telephone = null,
        ?string $addressStreet = null,
        ?string $addressPostcode = null,
        ?string $addressLocality = null,
        ?string $addressCountry = null,
        ?\DateTimeImmutable $birthDate = null,
        ?string $jobTitle = null,
        ?ExternalId $externalIdentifier = null
    ) {
        $this->uuid = $uuid;
        $this->username = $username;
        $this->familyName = $familyName;
        $this->givenName = $givenName;
        $this->gender = $gender;
        $this->recoverEmail = $recoverEmail;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->addressStreet = $addressStreet;
        $this->addressPostcode = $addressPostcode;
        $this->addressLocality = $addressLocality;
        $this->addressCountry = $addressCountry;
        $this->birthDate = $birthDate;
        $this->jobTitle = $jobTitle;
        $this->externalIdentifier = $externalIdentifier;
    }
}
