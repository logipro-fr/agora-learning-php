<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Identifier;

/**
 * Identifiant d'un objet dans un système externe, exposé sous la forme "sourceName:id".
 */
final class ExternalId implements ApiObjectIdentifier
{
    public const SOURCE_NAME_PATTERN = '/^[A-Za-z0-9_-]{1,50}$/';


    public const SEPARATOR = ':';

    private string $sourceName;
    private string $id;

    public function __construct(string $sourceName, string $id)
    {
        $sourceName = trim($sourceName);
        $id = trim($id);

        if (preg_match(self::SOURCE_NAME_PATTERN, $sourceName) !== 1) {
            throw new \InvalidArgumentException(sprintf(
                'ExternalId: invalid source name "%s" (expected 1 to 50 characters among A-Z, a-z, 0-9, "_" and "-").',
                $sourceName
            ));
        }

        if ($id === '') {
            throw new \InvalidArgumentException('ExternalId: the identifier must not be empty.');
        }

        $this->sourceName = $sourceName;
        $this->id = $id;
    }

    public function getSourceName(): string
    {
        return $this->sourceName;
    }

    public function getSourceId(): string
    {
        return $this->id;
    }

    public function getId(): string
    {
        return $this->sourceName . self::SEPARATOR . $this->id;
    }

    /**
     * Reconstruit l'identifiant à partir du format renvoyé par l'API ({sourceName, id}).
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        /** @var array{sourceName: string, id: string} $data */
        return new self($data['sourceName'], $data['id']);
    }

    /**
     * Représentation attendue par l'API dans le corps des requêtes de création.
     *
     * @return array{sourceName: string, id: string}
     */
    public function toArray(): array
    {
        return [
            'sourceName' => $this->sourceName,
            'id' => $this->id,
        ];
    }
}
