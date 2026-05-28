<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\ClientCore;

use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\ClientCore\TokenHandler;
use AgoraLearningPhp\Tests\Service\ServiceTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClientTest extends ServiceTestCase
{
    public function testRequestForwardsToUnderlyingClientWithAuthHeader(): void
    {
        $this->injectCachedToken('my-bearer-token');
        $tokenHandler   = new TokenHandler($this->createMock(HttpClientInterface::class));
        $mockResponse   = $this->createMock(ResponseInterface::class);

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://api.test.local/path',
                $this->callback(function (array $options) {
                    return isset($options['headers']['Authorization'])
                        && $options['headers']['Authorization'] === 'Bearer my-bearer-token'
                        && $options['headers']['Content-Type'] === 'application/json'
                        && $options['headers']['Accept'] === 'application/json';
                })
            )
            ->willReturn($mockResponse);

        $result = (new HttpClient($mockHttpClient, $tokenHandler))->request('GET', 'https://api.test.local/path', '');

        $this->assertSame($mockResponse, $result);
    }

    public function testRequestPassesJsonBodyToUnderlyingClient(): void
    {
        $this->injectCachedToken('token-xyz');
        $tokenHandler   = new TokenHandler($this->createMock(HttpClientInterface::class));
        $mockResponse   = $this->createMock(ResponseInterface::class);
        $body           = '{"name":"test"}';

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $this->anything(),
                $this->callback(function (array $options) use ($body) {
                    return $options['body'] === $body;
                })
            )
            ->willReturn($mockResponse);

        (new HttpClient($mockHttpClient, $tokenHandler))->request('POST', 'https://api.test.local/resource', $body);
    }

    public function testRequestSetsUserAgentHeader(): void
    {
        $this->injectCachedToken('tok');
        $tokenHandler   = new TokenHandler($this->createMock(HttpClientInterface::class));
        $mockResponse   = $this->createMock(ResponseInterface::class);

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(function (array $options) {
                    return isset($options['headers']['User-Agent'])
                        && $options['headers']['User-Agent'] === 'AgoraLearningInfinityClient/1.0';
                })
            )
            ->willReturn($mockResponse);

        (new HttpClient($mockHttpClient, $tokenHandler))->request('GET', 'http://x', '');
    }
}
