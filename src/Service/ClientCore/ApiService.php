<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\ClientCore;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;
use AgoraLearningPhp\Service\Tools\JsonHandler;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @template T of ApiOutput
 */
abstract class ApiService
{
    public const DATETIME_FORMAT = 'c';

    protected HttpClient $httpClient;

    public function __construct()
    {
        $curlClient = new CurlHttpClient();

        $this->httpClient = new HttpClient(
            $curlClient,
            new TokenHandler($curlClient)
        );
    }

    /**
     * @return T
     */
    protected function convertResponseToDTO(ResponseInterface $response): ApiOutput
    {
        /** @var array<string, mixed> $responseData */
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        if (HttpClient::isHttpSuccess($response)) {
            return static::convertDataToDTO($responseData);
        }

        $detail = $responseData['detail'] ?? null;
        $message = is_string($detail) ? $detail : 'An unexpected error occured';
        throw new AgoraLearningClientException($message, $response->getStatusCode());
    }

    /**
     * @return array<int, T>
     */
    protected function convertResponseToDTOarray(ResponseInterface $response): array
    {
        $result = [];
        /** @var array<string, mixed> $responseData */
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        if (HttpClient::isHttpSuccess($response)) {
            foreach ($responseData as $data) {
                /** @var array<string, mixed> $data */
                $result[] = static::convertDataToDTO($data);
            }
            return $result;
        }

        $detail = $responseData['detail'] ?? null;
        $message = is_string($detail) ? $detail : 'An unexpected error occured';
        throw new AgoraLearningClientException($message, $response->getStatusCode());
    }

    /**
     * Convertit la réponse d'une création (POST), qui ne contient que les identifiants de l'objet créé.
     */
    protected function convertResponsePostToDTO(ResponseInterface $response): CreatedOutput
    {
        /** @var array<string, mixed> $responseData */
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        if (HttpClient::isHttpSuccess($response)) {
            return self::convertPostDataToDTO($responseData);
        }

        $detail = $responseData['detail'] ?? null;
        $message = is_string($detail) ? $detail : 'An unexpected error occured';
        throw new AgoraLearningClientException($message, $response->getStatusCode());
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertPostDataToDTO(array $data): CreatedOutput
    {
        /** @var array{agoraId: string, sourceName?: string|null, externalId?: string|null} $data */
        $sourceName = $data['sourceName'] ?? null;
        $externalId = $data['externalId'] ?? null;

        return new CreatedOutput(
            new AgoraId($data['agoraId']),
            ($sourceName !== null && $externalId !== null) ? new ExternalId($sourceName, $externalId) : null
        );
    }

    /**
     * @param array<string, mixed> $data
     * @return T
     */
    abstract public static function convertDataToDTO(array $data): ApiOutput;
}
