<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Program;

use AgoraLearningPhp\DTO\Input\Program\ProgramBiLevelComplexList;
use AgoraLearningPhp\DTO\Input\Program\ProgramComplexList;
use AgoraLearningPhp\DTO\Input\Program\ProgramInput;
use AgoraLearningPhp\DTO\Input\Program\ProgramSimpleList;
use AgoraLearningPhp\DTO\Input\Program\ProgramText;
use AgoraLearningPhp\DTO\Output\CreatedOutput;
use AgoraLearningPhp\DTO\Output\Program\ProgramOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Exception\AgoraLearningClientException;
use AgoraLearningPhp\Identifier\ApiObjectIdentifier;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use AgoraLearningPhp\Service\Tools\JsonHandler;

/**
 * @extends ApiService<ProgramOutput>
 */
class Program extends ApiService
{
    /**
     * @throws AgoraLearningClientException si la session n'existe pas (404) ou n'a pas de parcours (422)
     */
    public function getProgram(ApiObjectIdentifier $sessionIdentifier): ProgramOutput
    {
        $body = '';
        $response = $this->httpClient->request(
            RequestMethod::GET,
            ApiUrls::getGetProgram($sessionIdentifier->getId()),
            $body
        );
        return $this->convertResponseToDTO($response);
    }

    /**
     * Remplace tout le programme de la session (PUT) : un type non renseigné n'est pas envoyé, donc vidé.
     *
     * @throws AgoraLearningClientException si la session n'existe pas (404), n'a pas de parcours (422) ou
     *                                      si Agora refuse le programme
     */
    public function putProgram(ApiObjectIdentifier $sessionIdentifier, ProgramInput $programInput): CreatedOutput
    {
        // Cast en objet : un programme vide doit être encodé "{}" et non "[]"
        $body = JsonHandler::jsonEncode((object) $this->buildBody($programInput));
        $response = $this->httpClient->request(
            RequestMethod::PUT,
            ApiUrls::getUpdateProgram($sessionIdentifier->getId()),
            $body
        );
        return $this->convertResponsePostToDTO($response);
    }

    /**
     * Vide le contenu du programme de la session (DELETE, réponse 204 sans contenu).
     *
     * @throws AgoraLearningClientException si la session n'existe pas (404) ou en cas d'erreur de l'API
     */
    public function deleteProgram(ApiObjectIdentifier $sessionIdentifier): void
    {
        $this->sendDelete(ApiUrls::getDeleteProgram($sessionIdentifier->getId()));
    }

    /**
     * @return array<string, mixed>
     */
    private function buildBody(ProgramInput $programInput): array
    {
        $types = array_merge(
            ProgramInput::COMPLEX_LIST_TYPES,
            ProgramInput::BI_LEVEL_COMPLEX_LIST_TYPES,
            ProgramInput::TEXT_TYPES,
            ProgramInput::SIMPLE_LIST_TYPES
        );

        $data = [];
        foreach ($types as $type) {
            /** @var ProgramText|ProgramSimpleList|ProgramComplexList|ProgramBiLevelComplexList|null $value */
            $value = $programInput->$type;
            if ($value !== null) {
                $data[$type] = $value->toArray();
            }
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): ProgramOutput
    {
        $program = new ProgramInput();

        foreach (ProgramInput::TEXT_TYPES as $type) {
            $value = $data[$type] ?? null;
            if (is_array($value)) {
                $program->$type = new ProgramText(self::stringOrEmpty($value['programText'] ?? null));
            }
        }
        foreach (ProgramInput::SIMPLE_LIST_TYPES as $type) {
            $value = $data[$type] ?? null;
            if (is_array($value)) {
                $program->$type = self::convertSimpleList($value);
            }
        }
        foreach (ProgramInput::COMPLEX_LIST_TYPES as $type) {
            $value = $data[$type] ?? null;
            if (is_array($value)) {
                $program->$type = self::convertComplexList($value);
            }
        }
        foreach (ProgramInput::BI_LEVEL_COMPLEX_LIST_TYPES as $type) {
            $value = $data[$type] ?? null;
            if (is_array($value)) {
                $program->$type = self::convertBiLevelComplexList($value);
            }
        }

        return new ProgramOutput(self::stringOrEmpty($data['sessionAgoraId'] ?? null), $program);
    }

    /**
     * @param array<mixed> $data
     */
    private static function convertSimpleList(array $data): ProgramSimpleList
    {
        $items = $data['programSimpleList'] ?? null;

        return new ProgramSimpleList(is_array($items) ? array_map([self::class, 'stringOrEmpty'], array_values($items)) : []);
    }

    /**
     * @param array<mixed> $data
     */
    private static function convertComplexList(array $data): ProgramComplexList
    {
        $list = $data['programSimpleList'] ?? null;

        return new ProgramComplexList(
            isset($data['description']) ? self::stringOrEmpty($data['description']) : null,
            is_array($list) ? self::convertSimpleList($list) : null
        );
    }

    /**
     * @param array<mixed> $data
     */
    private static function convertBiLevelComplexList(array $data): ProgramBiLevelComplexList
    {
        $elements = [];
        $list = $data['programComplexList'] ?? null;
        foreach (is_array($list) ? $list : [] as $element) {
            if (is_array($element)) {
                $elements[] = self::convertComplexList($element);
            }
        }

        return new ProgramBiLevelComplexList(
            isset($data['description']) ? self::stringOrEmpty($data['description']) : null,
            $elements
        );
    }

    /**
     * @param mixed $value
     */
    private static function stringOrEmpty($value): string
    {
        return is_string($value) ? $value : '';
    }
}
