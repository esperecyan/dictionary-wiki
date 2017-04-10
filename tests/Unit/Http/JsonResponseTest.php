<?php

namespace Tests\Unit\Http;

use Tests\TestCase;
use App\Http\JsonResponse;

class JsonResponseTest extends TestCase
{
    /**
     * @covers \App\Http\JsonResponse::__constaruct
     *
     * @param  mixed  $data
     * @param  string  $json
     * @return void
     *
     * @dataProvider jsonProvider
     */
    public function testConstruct($data, string $json): void
    {
        $this->assertSame($json, (new JsonResponse($data))->getContent());
    }
    
    public function jsonProvider(): array
    {
        return [
            [['https://resource.test/image.png'], "[\n    \"https://resource.test/image.png\"\n]"],
            [['title' => '𩸽']                  , "{\n    \"title\": \"𩸽\"\n}"                  ],
        ];
    }
}
