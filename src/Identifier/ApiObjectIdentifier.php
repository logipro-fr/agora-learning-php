<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Identifier;

/**
 * Identifiant d'un objet de l'API, utilisable dans un segment d'URL.
 *
 * Le discriminant entre les implémentations est la présence de ':' :
 * un AgoraId n'en contient jamais, un ExternalId en contient toujours au moins un.
 */
interface ApiObjectIdentifier
{
    /** Représentation de l'identifiant tel qu'il apparaît dans l'URL (avant encodage). */
    public function getId(): string;
}
