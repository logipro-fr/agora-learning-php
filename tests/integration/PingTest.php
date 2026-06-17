<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

class PingTest extends AbstractAgoraLearningTest
{
    public function testPing(): void
    {
        //Act
        $response = $this->client->ping();
        $statusCode = $response->getStatusCode();

        // Assert
        $this->assertEquals(200, $statusCode);
    }
}
