<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Identifier;

/**
 * Identifiant natif Agora (ex : ses_01KRXPMF97F8BRPH7N7972QRYY).
 */
final class AgoraId implements ApiObjectIdentifier
{
    private string $id;

    public function __construct(string $id)
    {
        $id = trim($id);

        if ($id === '') {
            throw new \InvalidArgumentException('AgoraId: the identifier must not be empty.');
        }

        if (strpos($id, ':') !== false) {
            throw new \InvalidArgumentException(sprintf(
                'AgoraId: "%s" contains ":" and cannot be an Agora identifier. Use ExternalId for an identifier from an external source.',
                $id
            ));
        }

        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
