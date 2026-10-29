<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\ClientCore;

use AgoraLearningPhp\DTO\Input\AbstractPatchInput;
use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\Enum\RequestMethod;
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
        $this->throwIfError($response);

        /** @var array<string, mixed> $responseData */
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        return static::convertDataToDTO($responseData);
    }

    /**
     * @return array<int, T>
     */
    protected function convertResponseToDTOarray(ResponseInterface $response): array
    {
        $this->throwIfError($response);

        $result = [];
        /** @var array<string, mixed> $responseData */
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        foreach ($responseData as $data) {
            /** @var array<string, mixed> $data */
            $result[] = static::convertDataToDTO($data);
        }
        return $result;
    }

    /**
     * Envoie une modification partielle (PATCH, application/merge-patch+json) : seuls les champs
     * renseignés dans $patchInput sont transmis.
     */
    protected function sendPatch(string $url, AbstractPatchInput $patchInput): CreatedOutput
    {
        // Cast en objet : un patch vide doit être encodé "{}" et non "[]"
        $body = JsonHandler::jsonEncode((object) $patchInput->toArray());
        $response = $this->httpClient->request(RequestMethod::PATCH, $url, $body);
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Convertit la réponse d'une écriture (POST, PUT, PATCH), qui ne contient que les identifiants de l'objet.
     */
    protected function convertResponsePostToDTO(ResponseInterface $response): CreatedOutput
    {
        $this->throwIfError($response);

        /** @var array<string, mixed> $responseData */
        $responseData = JsonHandler::jsonDecode($response->getContent(false), true);
        return self::convertPostDataToDTO($responseData);
    }

    /**
     * Envoie un détachement (PATCH /{ressource}/{id}/detach, réponse 204 sans contenu) : lève une exception
     * s'il a échoué. La route n'attend aucun champ, mais le PATCH exige un corps JSON (merge-patch).
     *
     * @throws AgoraLearningClientException si l'objet n'existe pas (404) ou si Agora refuse le détachement
     */
    protected function sendDetach(string $url): void
    {
        $body = '{}';
        $response = $this->httpClient->request(RequestMethod::PATCH, $url, $body);
        $this->assertResponseSuccess($response);
    }

    /**
     * Envoie une suppression (DELETE, réponse 204 sans contenu) : lève une exception si elle a échoué.
     *
     * @throws AgoraLearningClientException si l'objet n'existe pas (404) ou en cas d'erreur de l'API
     */
    protected function sendDelete(string $url): void
    {
        $body = '';
        $response = $this->httpClient->request(RequestMethod::DELETE, $url, $body);
        $this->assertResponseSuccess($response);
    }

    /**
     * Vérifie la réponse d'une opération sans contenu (DELETE, 204) : lève une exception si elle a échoué.
     */
    protected function assertResponseSuccess(ResponseInterface $response): void
    {
        $this->throwIfError($response);
    }

    /**
     * Lève une AgoraLearningClientException portant le code HTTP si la réponse est une erreur.
     *
     * Le motif est lu dans "detail" (erreurs API Platform) ou "error" (conflits, ex. identifiant externe
     * déjà utilisé) ; le corps d'une erreur n'étant pas forcément du JSON (erreur du serveur web, réponse
     * vide), le message par défaut précise alors le code HTTP.
     *
     * @throws AgoraLearningClientException
     */
    private function throwIfError(ResponseInterface $response): void
    {
        if (HttpClient::isHttpSuccess($response)) {
            return;
        }

        $statusCode = $response->getStatusCode();
        $responseData = json_decode($response->getContent(false), true);
        $reason = null;
        if (is_array($responseData)) {
            $reason = $responseData['detail'] ?? $responseData['error'] ?? null;
        }
        $message = is_string($reason) && $reason !== ''
            ? $reason
            : sprintf('An unexpected error occured (HTTP %d)', $statusCode);
        throw new AgoraLearningClientException($message, $statusCode);
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
