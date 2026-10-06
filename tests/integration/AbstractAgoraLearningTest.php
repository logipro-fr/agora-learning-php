<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\integration;

use AgoraLearningPhp\AgoraLearningClient;
use PHPUnit\Framework\TestCase;

abstract class AbstractAgoraLearningTest extends TestCase
{
    protected AgoraLearningClient $client;

    public static function setUpBeforeClass(): void
    {
        $baseUrl = self::getConfig('AGORA_BASE_URL');
        if ($baseUrl === '') {
            self::markTestSkipped('AGORA_BASE_URL non défini (.env.local ou variable d\'environnement)');
        }
        if (self::getConfig('AGORA_API_KEY') === '') {
            self::markTestSkipped('AGORA_API_KEY non défini (.env.local ou variable d\'environnement)');
        }

        $parts = parse_url($baseUrl);
        $host = $parts['host'] ?? '';
        $port = $parts['port'] ?? (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);

        $connection = @fsockopen($host, $port, $errno, $errstr, 3);
        if ($connection === false) {
            self::markTestSkipped("AGORA_BASE_URL \"{$baseUrl}\" is not accessible: {$errstr}");
        }
        fclose($connection);
    }

    protected function setUp(): void
    {
        $this->client = new AgoraLearningClient(
            self::getConfig('AGORA_BASE_URL'),
            self::getConfig('AGORA_API_KEY')
        );
    }

    /**
     * Ordre de priorité : variable d'environnement, puis .env.local, puis .env.
     */
    private static function getConfig(string $name): string
    {
        $env = getenv($name);
        if ($env !== false) {
            return $env;
        }

        $value = '';
        foreach (['.env', '.env.local'] as $file) {
            $values = self::parseEnvFile(dirname(__DIR__, 2) . '/' . $file);
            if (array_key_exists($name, $values)) {
                $value = $values[$name];
            }
        }

        return $value;
    }

    /**
     * Lecture minimale d'un fichier .env : lignes NOM=valeur, commentaires (#) ignorés,
     * guillemets simples ou doubles autour de la valeur retirés.
     *
     * @return array<string, string>
     */
    private static function parseEnvFile(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }

        $values = [];
        foreach ((array) file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim((string) $line);
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $value = trim($value);
            if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'") && substr($value, -1) === $value[0]) {
                $value = substr($value, 1, -1);
            }
            $values[trim($key)] = $value;
        }

        return $values;
    }
}
