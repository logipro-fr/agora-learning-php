<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\unit\Service;

use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\ClientCore\HttpClient;
use AgoraLearningPhp\Service\ClientCore\TokenHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Classe de base pour tous les tests de services qui font des appels HTTP.
 *
 * Elle centralise :
 * - La réinitialisation de l'état statique (ApiUrls, TokenHandler) avant/après chaque test
 * - La création d'un HttpClient mocké prêt à l'emploi
 *
 * Usage : étendre cette classe au lieu de TestCase directement.
 *
 *   class LearnerTest extends ServiceTestCase { ... }
 */
abstract class ServiceTestCase extends TestCase
{
    protected function setUp(): void
    {
        $this->resetStaticApiState();
        ApiUrls::initBaseUrl('https://api.test.local');
    }

    protected function tearDown(): void
    {
        $this->resetStaticApiState();
    }

    /**
     * Crée un HttpClient dont la réponse HTTP est entièrement simulée.
     *
     * @param int    $statusCode Code HTTP à retourner (ex: 200, 404, 500)
     * @param string $body       Contenu JSON de la réponse simulée
     */
    protected function makeHttpClientWithResponse(int $statusCode, string $body): HttpClient
    {
        $this->injectCachedToken('test-token');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn($statusCode);
        $mockResponse->method('getContent')->with(false)->willReturn($body);

        $mockInner = $this->createMock(HttpClientInterface::class);
        $mockInner->method('request')->willReturn($mockResponse);

        return new HttpClient($mockInner, new TokenHandler($mockInner));
    }

    /**
     * Crée un HttpClient qui capture le corps JSON envoyé dans la requête POST.
     * Permet de vérifier que tous les champs sont bien présents dans le body.
     *
     * @param int    $statusCode    Code HTTP à retourner
     * @param string $responseBody  Corps de la réponse simulée
     * @param string $capturedBody  Variable de référence où sera stocké le corps envoyé
     */
    protected function makeHttpClientInspectingBody(int $statusCode, string $responseBody, string &$capturedBody): HttpClient
    {
        $this->injectCachedToken('test-token');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn($statusCode);
        $mockResponse->method('getContent')->with(false)->willReturn($responseBody);

        $mockInner = $this->createMock(HttpClientInterface::class);
        $mockInner->method('request')
            ->willReturnCallback(function (string $method, string $url, array $options) use ($mockResponse, &$capturedBody) {
                $capturedBody = $options['body'] ?? '';
                return $mockResponse;
            });

        return new HttpClient($mockInner, new TokenHandler($mockInner));
    }

    /**
     * Crée un HttpClient qui capture la méthode HTTP et l'URL appelées.
     *
     * @param int    $statusCode   Code HTTP à retourner
     * @param string $responseBody Corps de la réponse simulée
     * @param string $capturedMethod Variable de référence où sera stockée la méthode
     * @param string $capturedUrl    Variable de référence où sera stockée l'URL
     */
    protected function makeHttpClientInspectingRequest(
        int $statusCode,
        string $responseBody,
        string &$capturedMethod,
        string &$capturedUrl
    ): HttpClient {
        $this->injectCachedToken('test-token');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn($statusCode);
        $mockResponse->method('getContent')->with(false)->willReturn($responseBody);

        $mockInner = $this->createMock(HttpClientInterface::class);
        $mockInner->method('request')
            ->willReturnCallback(function (string $method, string $url) use ($mockResponse, &$capturedMethod, &$capturedUrl) {
                $capturedMethod = $method;
                $capturedUrl = $url;
                return $mockResponse;
            });

        return new HttpClient($mockInner, new TokenHandler($mockInner));
    }

    /**
     * Injecte un token valide et non expiré dans l'état statique de TokenHandler.
     * Cela évite que le TokenHandler tente une requête réseau pour s'authentifier.
     */
    protected function injectCachedToken(string $token): void
    {
        $ref = new \ReflectionClass(TokenHandler::class);

        $tokenProp = $ref->getProperty('token');
        if (\PHP_VERSION_ID < 80100) {
            $tokenProp->setAccessible(true);
        }
        $tokenProp->setValue(null, $token);

        $expireProp = $ref->getProperty('expireAt');
        if (\PHP_VERSION_ID < 80100) {
            $expireProp->setAccessible(true);
        }
        $expireProp->setValue(null, (new \DateTimeImmutable())->getTimestamp() + 3600);
    }

    /**
     * Remet à zéro toutes les propriétés statiques de ApiUrls et TokenHandler.
     * Indispensable pour garantir l'isolation entre les tests.
     */
    protected function resetStaticApiState(): void
    {
        $refUrls = new \ReflectionClass(ApiUrls::class);
        $baseUrl = $refUrls->getProperty('baseUrl');
        if (\PHP_VERSION_ID < 80100) {
            $baseUrl->setAccessible(true);
        }
        $baseUrl->setValue(null, '');

        $refToken = new \ReflectionClass(TokenHandler::class);
        foreach (['apiKey', 'token', 'expireAt'] as $name) {
            $prop = $refToken->getProperty($name);
            if (\PHP_VERSION_ID < 80100) {
                $prop->setAccessible(true);
            }
            $prop->setValue(null, $name === 'apiKey' ? '' : null);
        }
    }
}
