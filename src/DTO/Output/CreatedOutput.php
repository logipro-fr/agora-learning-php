<?php

declare(strict_types=1);

namespace AgoraLearningPhp\DTO\Output;

use AgoraLearningPhp\Identifier\AgoraId;
use AgoraLearningPhp\Identifier\ExternalId;

/**
 * Réponse de l'API à une requête d'écriture (création POST, remplacement PUT, modification PATCH) :
 * identifiants de l'objet créé ou modifié.
 */
final class CreatedOutput
{
    public AgoraId $agoraId;
    public ?ExternalId $externalId;

    public function __construct(AgoraId $agoraId, ?ExternalId $externalId = null)
    {
        $this->agoraId = $agoraId;
        $this->externalId = $externalId;
    }
}
