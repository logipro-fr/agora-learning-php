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
        $configs = include __DIR__ . '/config.php';
        $baseUrl = $configs['BASE_URL'];

        $parts = parse_url($baseUrl);
        $host = $parts['host'] ?? '';
        $port = $parts['port'] ?? (($parts['scheme'] ?? 'http') === 'https' ? 443 : 80);

        $connection = @fsockopen($host, $port, $errno, $errstr, 3);
        if ($connection === false) {
            self::markTestSkipped("BASE_URL \"{$baseUrl}\" is not accessible: {$errstr}");
        }
        fclose($connection);
    }

    protected function setUp(): void
    {
        $configs = include __DIR__ . '/config.php';
        $this->client = new AgoraLearningClient($configs['BASE_URL'], $configs['API_KEY']);
    }
}
