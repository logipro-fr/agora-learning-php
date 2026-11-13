<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Identifier;

final class ApiObjectIdentifierFactory
{
    /**
     * Reconstruit un identifiant à partir de sa représentation getId().
     */
    public static function fromString(string $value): ApiObjectIdentifier
    {
        if (strpos($value, ExternalId::SEPARATOR) === false) {
            return new AgoraId($value);
        }

        // Limite à 2 : l'id externe peut lui-même contenir ':'
        [$sourceName, $id] = explode(ExternalId::SEPARATOR, $value, 2);

        return new ExternalId($sourceName, $id);
    }
}
