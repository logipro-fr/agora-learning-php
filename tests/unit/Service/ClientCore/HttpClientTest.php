<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\ClientCore;

use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\ClientCore\TokenHandler;
use AgoraLearningPhp\Tests\unit\Service\ServiceTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClientTest extends ServiceTestCase
{
    public function testRequestForwardsToUnderlyingClientWithAuthHeader(): void
    {
        // Arrange
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

        // Act
        $result = (new HttpClient($mockHttpClient, $tokenHandler))->request('GET', 'https://api.test.local/path', '');

        // Assert
        $this->assertSame($mockResponse, $result);
    }

    public function testRequestPassesJsonBodyToUnderlyingClient(): void
    {
        // Arrange
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

        // Act
        (new HttpClient($mockHttpClient, $tokenHandler))->request('POST', 'https://api.test.local/resource', $body);

        // Assert — verified via mock expectation above
    }

    public function testRequestSetsUserAgentHeader(): void
    {
        // Arrange
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

        // Act
        (new HttpClient($mockHttpClient, $tokenHandler))->request('GET', 'http://x', '');

        // Assert — verified via mock expectation above
    }

    public function testIsHttpSuccessReturnsFalseFor199(): void
    {
        // Arrange
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(199);

        // Act
        $result = HttpClient::isHttpSuccess($response);

        // Assert
        $this->assertFalse($result);
    }

    public function testIsHttpSuccessReturnsTrueFor200(): void
    {
        // Arrange
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        // Act
        $result = HttpClient::isHttpSuccess($response);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsHttpSuccessReturnsTrueFor299(): void
    {
        // Arrange
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(299);

        // Act
        $result = HttpClient::isHttpSuccess($response);

        // Assert
        $this->assertTrue($result);
    }

    public function testIsHttpSuccessReturnsFalseFor300(): void
    {
        // Arrange
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(300);

        // Act
        $result = HttpClient::isHttpSuccess($response);

        // Assert
        $this->assertFalse($result);
    }
}
