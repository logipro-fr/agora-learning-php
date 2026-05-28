<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\ClientCore;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClient
{
    protected HttpClientInterface $httpClient;
    protected TokenHandler $tokenHandler;

    public function __construct(HttpClientInterface $httpClient, TokenHandler $tokenHandler)
    {
        $this->httpClient = $httpClient;
        $this->tokenHandler = $tokenHandler;
    }

    public function request(string $method, string $url, string $jsonBody): ResponseInterface
    {
        $token = $this->tokenHandler->getToken();

        return $this->doRequest($method, $url, $jsonBody, $token);
    }

    protected function doRequest(string $method, string $url, string $jsonBody, string $token): ResponseInterface
    {
        return $this->httpClient->request(
            $method,
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'AgoraLearningInfinityClient/1.0',
                    'Authorization' => 'Bearer ' . $token
                ],
                'body' => $jsonBody
            ]
        );
    }

    public static function isHttpSuccess(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return ($statusCode > 199 && $statusCode < 300);
    }
}
