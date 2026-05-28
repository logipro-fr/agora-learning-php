<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\Auth;

use AgoraLearningPhp\Service\Auth\Ping;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\ClientCore\TokenHandler;
use AgoraLearningPhp\Tests\Service\ServiceTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PingTest extends ServiceTestCase
{
    public function testPingCallsGetOnPingEndpointAndReturns200(): void
    {
        $this->injectCachedToken('test-token');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);

        $mockInner = $this->createMock(HttpClientInterface::class);
        $mockInner->expects($this->once())
            ->method('request')
            ->with('GET', $this->stringContains('/ping'), $this->anything())
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($mockInner, new TokenHandler($mockInner));

        $ping = new class($httpClient) extends Ping {
            public function __construct(HttpClient $client)
            {
                $this->httpClient = $client;
            }
        };

        $result = $ping->ping();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }
}
