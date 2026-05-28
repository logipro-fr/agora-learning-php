<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\ClientCore;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Service\Tools\JsonHandler;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;

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

    protected function convertResponseToDTO(ResponseInterface $response): ApiOutput
    {
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        if (HttpClient::isHttpSuccess($response)) {
            return static::convertDataToDTO($responseData);
        }

        $message = array_key_exists('detail', $responseData) ? $responseData['detail'] : 'An unexpected error occured';
        throw new AgoraLearningClientException($message);
    }

    /**
     * @return array<int, ApiOutput>
     */
    protected function convertResponseToDTOarray(ResponseInterface $response): array
    {
        $result = [];
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        if (HttpClient::isHttpSuccess($response)) {
            foreach ($responseData as $data) {
                $result[] = static::convertDataToDTO($data);
            }
            return $result;
        }

        $message = array_key_exists('detail', $responseData) ? $responseData['detail'] : 'An unexpected error occured';
        throw new AgoraLearningClientException($message);
    }

    abstract public static function convertDataToDTO(array $data): ApiOutput;
}
