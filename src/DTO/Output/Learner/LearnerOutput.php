<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output\Learner;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\Enum\Gender;

final class LearnerOutput extends ApiOutput
{
    public string $id;
    public string $username;
    public string $familyName;
    public string $givenName;
    public string $gender;
    public string $recoverEmail;
    public ?string $email;
    public ?string $telephone;
    public ?string $addressStreet;
    public ?string $addressPostcode;
    public ?string $addressLocality;
    public ?string $addressCountry;
    public ?string $image;
    public ?\DateTimeImmutable $birthDate;
    public ?string $jobTitle;

    public function __construct(
        string $id,
        string $username,
        string $familyName,
        string $givenName,
        string $gender = Gender::GENDER_NA,
        string $recoverEmail = '',
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
        $this->id = $id;
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
        $this->image = $image;
        $this->birthDate = $birthDate;
        $this->jobTitle = $jobTitle;
    }
}
