<?php

declare(strict_types=1);

namespace AgoraLearningPhp\Tests\Enum;

use AgoraLearningPhp\Enum\RequestMethod;
use PHPUnit\Framework\TestCase;

class RequestMethodTest extends TestCase
{
    public function provideRequestMethodConstants(): array
    {
        return [
            'POST'   => ['POST',   RequestMethod::POST],
            'GET'    => ['GET',    RequestMethod::GET],
            'PUT'    => ['PUT',    RequestMethod::PUT],
            'DELETE' => ['DELETE', RequestMethod::DELETE],
        ];
    }

    /** @dataProvider provideRequestMethodConstants */
    public function testRequestMethodConstantHasExpectedValue(string $expected, string $actual): void
    {
        $this->assertSame($expected, $actual);
    }

    public function testConstantsAreDistinct(): void
    {
        $methods = [RequestMethod::POST, RequestMethod::GET, RequestMethod::PUT, RequestMethod::DELETE];
        $this->assertSame(count($methods), count(array_unique($methods)));
    }
}
