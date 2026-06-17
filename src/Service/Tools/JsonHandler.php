<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Service\Tools;

class JsonHandler
{
    /**
     * @param mixed $value
     * @param int $flags
     * @param int<1, max> $depth
     */
    public static function jsonEncode(
        $value,
        int $flags = JSON_UNESCAPED_UNICODE,
        int $depth = 512
    ): string {
        try {
            return json_encode($value, $flags | JSON_THROW_ON_ERROR, $depth);
        } catch (\JsonException $e) {
            throw new \RuntimeException(
                'Json_encode error: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * @param int<1, max> $depth
     * @return mixed
     */
    public static function jsonDecode(
        string $json,
        ?bool $associative = null,
        int $depth = 512,
        int $flags = 0
    ) {
        $data = json_decode($json, $associative, $depth, $flags);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                'Json_decode error: ' . json_last_error_msg()
                    . ' | Response(200): ' . substr($json, 0, 200)
            );
        }

        return $data;
    }
}
