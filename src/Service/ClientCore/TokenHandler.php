<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\ClientCore;

use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Service\Tools\JsonHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TokenHandler
{
    protected static string $apiKey = '';
    protected static ?string $token = null;
    protected static ?int $expireAt = null;

    protected HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getToken(): string
    {
        if (!$this->isTokenValid()) {
            $this->requestToken();
        }

        assert(is_string(self::$token));
        return self::$token;
    }

    protected function isTokenValid(): bool
    {
        if (empty(self::$token)) {
            return false;
        }

        $timeLimit = (new \DateTimeImmutable())->getTimestamp() - 60;
        if (self::$expireAt < $timeLimit) {
            return false;
        }

        return true;
    }

    protected function requestToken(): void
    {
        $apiKey = self::$apiKey;
        $response = $this->httpClient->request(
            RequestMethod::POST,
            ApiUrls::getGetToken(),
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'AgoraLearningClient/1.0',
                ],
                'body' => JsonHandler::jsonEncode(['api_key' => $apiKey])
            ]
        );

        if (200 === $response->getStatusCode()) {
            /** @var array{token: string, expires_in: int} $data */
            $data = JsonHandler::jsonDecode($response->getContent(), true);
            self::$token = $data['token'];
            self::$expireAt = (new \DateTimeImmutable())->getTimestamp() + $data['expires_in'];
        } else {
            self::$token = null;
            self::$expireAt = null;
            throw new \RuntimeException('Authentication failed (HTTP ' . $response->getStatusCode() . '): unable to retrieve token.');
        }
    }

    public static function initApiKey(string $apiKey): void
    {
        if (self::$apiKey === '') {
            self::$apiKey = $apiKey;
        }
    }
}
