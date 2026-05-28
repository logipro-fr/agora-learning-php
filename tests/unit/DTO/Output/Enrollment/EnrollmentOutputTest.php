<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Output\Enrollment;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\Enrollment\EnrollmentOutput;
use AgoraLearningPhp\DTO\Output\Person\PersonOutput;
use AgoraLearningPhp\DTO\Output\Session\FixedSessionOutput;
use AgoraLearningPhp\DTO\Output\Session\SessionOutput;
use AgoraLearningPhp\Enum\SessionMode;
use AgoraLearningPhp\Enum\SessionType;
use PHPUnit\Framework\TestCase;

class EnrollmentOutputTest extends TestCase
{
    private function makeLearner(): PersonOutput
    {
        return new PersonOutput('learner-uuid', 'Doe', 'John');
    }

    private function makeSession(): SessionOutput
    {
        $sessionData = new FixedSessionOutput(
            new \DateTimeImmutable('2024-01-01'),
            new \DateTimeImmutable('2024-12-31')
        );
        return new SessionOutput(
            'sess-uuid',
            'Formation',
            SessionType::SESSION_TYPE_INTER,
            SessionMode::SESSION_MODE_E_LEARNING,
            $sessionData
        );
    }

    public function testConstructorWithRequiredParamsOnly(): void
    {
        $learner = $this->makeLearner();
        $session = $this->makeSession();

        $output = new EnrollmentOutput('enroll-uuid-001', $learner, $session);

        $this->assertSame('enroll-uuid-001', $output->uuid);
        $this->assertSame($learner, $output->learner);
        $this->assertSame($session, $output->session);
        $this->assertNull($output->availabilityStartDate);
        $this->assertNull($output->availabilityEndDate);
    }

    public function testConstructorWithDates(): void
    {
        $learner = $this->makeLearner();
        $session = $this->makeSession();
        $start   = new \DateTimeImmutable('2024-02-01');
        $end     = new \DateTimeImmutable('2024-10-31');

        $output = new EnrollmentOutput('enroll-uuid-002', $learner, $session, $start, $end);

        $this->assertSame($start, $output->availabilityStartDate);
        $this->assertSame($end, $output->availabilityEndDate);
    }

    public function testExtendsApiOutput(): void
    {
        $output = new EnrollmentOutput('id', $this->makeLearner(), $this->makeSession());
        $this->assertInstanceOf(ApiOutput::class, $output);
    }
}
