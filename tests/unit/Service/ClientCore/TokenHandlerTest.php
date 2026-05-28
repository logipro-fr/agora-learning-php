<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\ClientCore;

use AgoraLearningPhp\Service\ClientCore\TokenHandler;
use AgoraLearningPhp\Tests\Service\ServiceTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TokenHandlerTest extends ServiceTestCase
{
    public function testInitApiKeySetsKeyOnce(): void
    {
        TokenHandler::initApiKey('my-secret-key');

        $ref  = new \ReflectionClass(TokenHandler::class);
        $prop = $ref->getProperty('apiKey');
        $prop->setAccessible(true);
        $this->assertSame('my-secret-key', $prop->getValue(null));
    }

    public function testInitApiKeyDoesNotOverwriteExistingKey(): void
    {
        TokenHandler::initApiKey('first-key');
        TokenHandler::initApiKey('second-key');

        $ref  = new \ReflectionClass(TokenHandler::class);
        $prop = $ref->getProperty('apiKey');
        $prop->setAccessible(true);
        $this->assertSame('first-key', $prop->getValue(null));
    }

    public function testGetTokenRequestsNewTokenWhenNoneExists(): void
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getContent')->willReturn('{"token":"abc123","expires_in":3600}');

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);

        $token = (new TokenHandler($mockHttpClient))->getToken();

        $this->assertSame('abc123', $token);
    }

    public function testGetTokenReturnsExistingValidToken(): void
    {
        $ref = new \ReflectionClass(TokenHandler::class);

        $tokenProp = $ref->getProperty('token');
        $tokenProp->setAccessible(true);
        $tokenProp->setValue(null, 'valid-cached-token');

        $expireProp = $ref->getProperty('expireAt');
        $expireProp->setAccessible(true);
        $expireProp->setValue(null, (new \DateTimeImmutable())->getTimestamp() + 3600);

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->never())->method('request');

        $this->assertSame('valid-cached-token', (new TokenHandler($mockHttpClient))->getToken());
    }

    public function testGetTokenRefreshesExpiredToken(): void
    {
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
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);

        $this->assertSame('new-token', (new TokenHandler($mockHttpClient))->getToken());
    }

    public function testGetTokenThrowsRuntimeExceptionOnAuthFailure(): void
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(401);
        $mockResponse->method('getContent')->willReturn('{"error":"Unauthorized"}');

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Authentication failed \(HTTP 401\)/');

        (new TokenHandler($mockHttpClient))->getToken();
    }

    public function testTokenNullifiedOnAuthFailure(): void
    {
        $ref = new \ReflectionClass(TokenHandler::class);

        $tokenProp = $ref->getProperty('token');
        $tokenProp->setAccessible(true);
        $tokenProp->setValue(null, 'stale-token');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(500);
        $mockResponse->method('getContent')->willReturn('{}');

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        $this->expectException(\RuntimeException::class);

        try {
            (new TokenHandler($mockHttpClient))->getToken();
        } finally {
            $this->assertNull($tokenProp->getValue(null));
        }
    }
}
