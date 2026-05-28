<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\DTO\Input\Enrollment;

use AgoraLearningPhp\DTO\Input\Enrollment\EnrollmentInput;
use PHPUnit\Framework\TestCase;

class EnrollmentInputTest extends TestCase
{
    public function testConstructorWithInviteTrue(): void
    {
        $input = new EnrollmentInput(
            'session-uuid-123',
            'learner-uuid-456',
            true
        );

        $this->assertSame('session-uuid-123', $input->sessionUuid);
        $this->assertSame('learner-uuid-456', $input->learnerUuid);
        $this->assertTrue($input->sendingInvite);
    }

    public function testConstructorWithInviteFalse(): void
    {
        $input = new EnrollmentInput(
            'session-uuid-abc',
            'learner-uuid-def',
            false
        );

        $this->assertSame('session-uuid-abc', $input->sessionUuid);
        $this->assertSame('learner-uuid-def', $input->learnerUuid);
        $this->assertFalse($input->sendingInvite);
    }
}
