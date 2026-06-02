<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service\ClientCore;

use AgoraLearningPhp\Service\ClientCore\TokenHandler;
use AgoraLearningPhp\Tests\unit\Service\ServiceTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TokenHandlerTest extends ServiceTestCase
{
    public function testInitApiKeySetsKeyOnce(): void
    {
        // Act
        TokenHandler::initApiKey('my-secret-key');

        // Assert
        $ref  = new \ReflectionClass(TokenHandler::class);
        $prop = $ref->getProperty('apiKey');
        $prop->setAccessible(true);
        $this->assertSame('my-secret-key', $prop->getValue(null));
    }

    public function testInitApiKeyDoesNotOverwriteExistingKey(): void
    {
        // Arrange
        TokenHandler::initApiKey('first-key');

        // Act
        TokenHandler::initApiKey('second-key');

        // Assert
        $ref  = new \ReflectionClass(TokenHandler::class);
        $prop = $ref->getProperty('apiKey');
        $prop->setAccessible(true);
        $this->assertSame('first-key', $prop->getValue(null));
    }

    public function testGetTokenRequestsNewTokenWhenNoneExists(): void
    {
        // Arrange
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getContent')->willReturn('{"token":"abc123","expires_in":3600}');
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())->method('request')->willReturn($mockResponse);

        // Act
        $token = (new TokenHandler($mockHttpClient))->getToken();

        // Assert
        $this->assertSame('abc123', $token);
    }

    public function testGetTokenReturnsExistingValidToken(): void
    {
        // Arrange
        $ref = new \ReflectionClass(TokenHandler::class);
        $tokenProp = $ref->getProperty('token');
        $tokenProp->setAccessible(true);
        $tokenProp->setValue(null, 'valid-cached-token');
        $expireProp = $ref->getProperty('expireAt');
        $expireProp->setAccessible(true);
        $expireProp->setValue(null, (new \DateTimeImmutable())->getTimestamp() + 3600);
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->never())->method('request');

        // Act
        $token = (new TokenHandler($mockHttpClient))->getToken();

        // Assert
        $this->assertSame('valid-cached-token', $token);
    }

    public function testGetTokenRefreshesExpiredToken(): void
    {
        // Arrange
        $ref = new \ReflectionClass(TokenHandler::class);
        $tokenProp = $ref->getProperty('token');
        $tokenProp->setAccessible(true);
        $tokenProp->setValue(null, 'old-token');
        $expireProp = $ref->getProperty('expireAt');
        $expireProp->setAccessible(true);
        $expireProp->setValue(null, (new \DateTimeImmutable())->getTimestamp() - 200);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getContent')->willReturn('{"token":"new-token","expires_in":3600}');
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())->method('request')->willReturn($mockResponse);

        // Act
        $token = (new TokenHandler($mockHttpClient))->getToken();

        // Assert
        $this->assertSame('new-token', $token);
    }

    public function testGetTokenThrowsRuntimeExceptionOnAuthFailure(): void
    {
        // Arrange
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(401);
        $mockResponse->method('getContent')->willReturn('{"error":"Unauthorized"}');
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Authentication failed \(HTTP 401\)/');

        // Act
        (new TokenHandler($mockHttpClient))->getToken();
    }

    public function testTokenNullifiedOnAuthFailure(): void
    {
        // Arrange
        $ref = new \ReflectionClass(TokenHandler::class);
        $tokenProp = $ref->getProperty('token');
        $tokenProp->setAccessible(true);
        $tokenProp->setValue(null, 'stale-token');
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(500);
        $mockResponse->method('getContent')->willReturn('{}');
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        // Assert
        $this->expectException(\RuntimeException::class);

        // Act + Assert (token nullifié dans le finally)
        try {
            (new TokenHandler($mockHttpClient))->getToken();
        } finally {
            $this->assertNull($tokenProp->getValue(null));
        }
    }

    public function testTokenAtExactTimeLimitIsStillValid(): void
    {
        // Arrange
        $ref = new \ReflectionClass(TokenHandler::class);
        $tokenProp = $ref->getProperty('token');
        $tokenProp->setAccessible(true);
        $tokenProp->setValue(null, 'boundary-token');
        $expireProp = $ref->getProperty('expireAt');
        $expireProp->setAccessible(true);
        $expireProp->setValue(null, (new \DateTimeImmutable())->getTimestamp() - 60);
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->never())->method('request');

        // Act
        $token = (new TokenHandler($mockHttpClient))->getToken();

        // Assert
        $this->assertSame('boundary-token', $token);
    }

    public function testTokenJustPastTimeLimitIsExpired(): void
    {
        // Arrange — expireAt == now - 61 : le token est expiré (expireAt < now - 60)
        $ref = new \ReflectionClass(TokenHandler::class);
        $tokenProp = $ref->getProperty('token');
        $tokenProp->setAccessible(true);
        $tokenProp->setValue(null, 'expiring-token');
        $expireProp = $ref->getProperty('expireAt');
        $expireProp->setAccessible(true);
        $expireProp->setValue(null, (new \DateTimeImmutable())->getTimestamp() - 61);
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getContent')->willReturn('{"token":"refreshed","expires_in":3600}');
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())->method('request')->willReturn($mockResponse);

        // Act
        $token = (new TokenHandler($mockHttpClient))->getToken();

        // Assert
        $this->assertSame('refreshed', $token);
    }

    public function testRequestTokenSendsApiKeyInBody(): void
    {
        // Arrange
        TokenHandler::initApiKey('my-secret-key');
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getContent')->willReturn('{"token":"tok","expires_in":3600}');
        $capturedOptions = [];
        $mockHttpClient  = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturnCallback(function ($_method, $_url, $options) use ($mockResponse, &$capturedOptions) {
                $capturedOptions = $options;
                return $mockResponse;
            });

        // Act
        (new TokenHandler($mockHttpClient))->getToken();

        // Assert
        $body = json_decode($capturedOptions['body'], true);
        $this->assertArrayHasKey('api_key', $body);
        $this->assertSame('my-secret-key', $body['api_key']);
    }

    public function testRequestTokenSendsAcceptAndUserAgentHeaders(): void
    {
        // Arrange
        TokenHandler::initApiKey('test-key');
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getContent')->willReturn('{"token":"tok","expires_in":3600}');
        $capturedOptions = [];
        $mockHttpClient  = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturnCallback(function ($_method, $_url, $options) use ($mockResponse, &$capturedOptions) {
                $capturedOptions = $options;
                return $mockResponse;
            });

        // Act
        (new TokenHandler($mockHttpClient))->getToken();

        // Assert
        $this->assertArrayHasKey('Accept', $capturedOptions['headers']);
        $this->assertArrayHasKey('User-Agent', $capturedOptions['headers']);
        $this->assertSame('application/json', $capturedOptions['headers']['Accept']);
        $this->assertSame('AgoraLearningClient/1.0', $capturedOptions['headers']['User-Agent']);
    }

    public function testRequestTokenSetsExpireAtToCurrentTimePlusExpiresIn(): void
    {
        // Arrange
        TokenHandler::initApiKey('test-key');
        $expiresIn = 1800;
        $before    = (new \DateTimeImmutable())->getTimestamp();
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getContent')->willReturn('{"token":"tok","expires_in":' . $expiresIn . '}');
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        // Act
        (new TokenHandler($mockHttpClient))->getToken();

        // Assert
        $after      = (new \DateTimeImmutable())->getTimestamp();
        $ref        = new \ReflectionClass(TokenHandler::class);
        $expireProp = $ref->getProperty('expireAt');
        $expireProp->setAccessible(true);
        $expireAt   = $expireProp->getValue(null);
        $this->assertGreaterThanOrEqual($before + $expiresIn, $expireAt);
        $this->assertLessThanOrEqual($after + $expiresIn, $expireAt);
    }
}
