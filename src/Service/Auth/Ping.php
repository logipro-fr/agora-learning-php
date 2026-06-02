<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Auth;

use AgoraLearningPhp\DTO\Output\ApiOutput;
use AgoraLearningPhp\Enum\RequestMethod;
use AgoraLearningPhp\Service\ClientCore\ApiService;
use AgoraLearningPhp\Service\ClientCore\ApiUrls;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @extends ApiService<ApiOutput>
 */
class Ping extends ApiService
{
    public function ping(): ResponseInterface
    {
        return $this->httpClient->request(RequestMethod::GET, ApiUrls::getPing(), '');
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function convertDataToDTO(array $data): ApiOutput
    {
        return new ApiOutput();
    }
}
