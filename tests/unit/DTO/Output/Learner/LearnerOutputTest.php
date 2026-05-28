<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Learner;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Learner\LearnerOutput;
use AgoraLearningPhp\Enum\Gender;
use PHPUnit\Framework\TestCase;

class LearnerOutputTest extends TestCase
{
    public function testConstructorWithRequiredParamsOnly(): void
    {
        $output = new LearnerOutput('uuid-123', 'jdupont', 'Dupont', 'Jean');

        $this->assertSame('uuid-123', $output->id);
        $this->assertSame('jdupont', $output->username);
        $this->assertSame('Dupont', $output->familyName);
        $this->assertSame('Jean', $output->givenName);
        $this->assertSame(Gender::GENDER_NA, $output->gender);
        $this->assertSame('', $output->recoverEmail);
        $this->assertNull($output->email);
        $this->assertNull($output->telephone);
        $this->assertNull($output->addressStreet);
        $this->assertNull($output->addressPostcode);
        $this->assertNull($output->addressLocality);
        $this->assertNull($output->addressCountry);
        $this->assertNull($output->image);
        $this->assertNull($output->birthDate);
        $this->assertNull($output->jobTitle);
    }

    public function testConstructorWithAllParams(): void
    {
        $birthDate = new \DateTimeImmutable('1992-03-10');

        $output = new LearnerOutput(
            'uuid-456',
            'mmartin',
            'Martin',
            'Marie',
            Gender::GENDER_FEMALE,
            'marie@recover.com',
            'marie@work.com',
            '+33611223344',
            '3 impasse des Tests',
            '31000',
            'Toulouse',
            'FR',
            'http://img.example.com/marie.jpg',
            $birthDate,
            'QA Engineer'
        );

        $this->assertSame('uuid-456', $output->id);
        $this->assertSame('mmartin', $output->username);
        $this->assertSame('Martin', $output->familyName);
        $this->assertSame('Marie', $output->givenName);
        $this->assertSame(Gender::GENDER_FEMALE, $output->gender);
        $this->assertSame('marie@recover.com', $output->recoverEmail);
        $this->assertSame('marie@work.com', $output->email);
        $this->assertSame('+33611223344', $output->telephone);
        $this->assertSame('3 impasse des Tests', $output->addressStreet);
        $this->assertSame('31000', $output->addressPostcode);
        $this->assertSame('Toulouse', $output->addressLocality);
        $this->assertSame('FR', $output->addressCountry);
        $this->assertSame('http://img.example.com/marie.jpg', $output->image);
        $this->assertSame($birthDate, $output->birthDate);
        $this->assertSame('QA Engineer', $output->jobTitle);
    }

    public function testExtendsApiOutput(): void
    {
        $output = new LearnerOutput('id', 'user', 'F', 'G');
        $this->assertInstanceOf(ApiOutput::class, $output);
    }
}
