<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Service\Tools;

use AgoraLearningPhp\Service\Tools\JsonHandler;
use PHPUnit\Framework\TestCase;

class JsonHandlerTest extends TestCase
{
    public function testJsonEncodeSimpleArray(): void
    {
        $result = JsonHandler::jsonEncode(['key' => 'value']);
        $this->assertSame('{"key":"value"}', $result);
    }

    public function testJsonEncodeNestedArray(): void
    {
        $result = JsonHandler::jsonEncode(['a' => ['b' => 1]]);
        $this->assertSame('{"a":{"b":1}}', $result);
    }

    public function testJsonEncodeWithUnicode(): void
    {
        $result = JsonHandler::jsonEncode(['name' => 'éàü']);
        $this->assertStringContainsString('éàü', $result);
    }

    public function testJsonEncodeNullValue(): void
    {
        $result = JsonHandler::jsonEncode(['field' => null]);
        $this->assertSame('{"field":null}', $result);
    }

    public function testJsonEncodeBoolValues(): void
    {
        $result = JsonHandler::jsonEncode(['active' => true, 'deleted' => false]);
        $this->assertSame('{"active":true,"deleted":false}', $result);
    }

    public function testJsonEncodeThrowsRuntimeExceptionOnInvalidValue(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Json_encode error/');
        JsonHandler::jsonEncode(NAN);
    }

    public function testJsonDecodeSimpleJson(): void
    {
        $result = JsonHandler::jsonDecode('{"key":"value"}', true);
        $this->assertSame(['key' => 'value'], $result);
    }

    public function testJsonDecodeAsObject(): void
    {
        $result = JsonHandler::jsonDecode('{"key":"value"}');
        $this->assertIsObject($result);
        $this->assertSame('value', $result->key);
    }

    public function testJsonDecodeNestedJson(): void
    {
        $result = JsonHandler::jsonDecode('{"a":{"b":42}}', true);
        $this->assertSame(42, $result['a']['b']);
    }

    public function testJsonDecodeNullField(): void
    {
        $result = JsonHandler::jsonDecode('{"field":null}', true);
        $this->assertNull($result['field']);
    }

    public function testJsonDecodeThrowsRuntimeExceptionOnInvalidJson(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Json_decode error/');
        JsonHandler::jsonDecode('{invalid_json}', true);
    }

    public function testJsonDecodeArrayJson(): void
    {
        $result = JsonHandler::jsonDecode('[1,2,3]', true);
        $this->assertSame([1, 2, 3], $result);
    }
}
