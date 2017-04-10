<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use App\Http\Middleware\Polyfill;
use App\Http\JsonResponse;
use Illuminate\Http\{Request, Response};
use Symfony\Component\HttpFoundation\{Response as BaseResponse, ResponseHeaderBag};

class PolyfillTest extends TestCase
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  string[]  $headers
     * @return void
     *
     * @dataProvider headersProvider
     */
    public function testHandle(Request $request, BaseResponse $response, array $headers): void
    {
        $actual = (new Polyfill())->handle($request, function () use ($response) {
            return $response;
        })->headers->all();
        $this->assertArraySubset((new ResponseHeaderBag($headers))->all(), $actual, true, print_r($actual, true));
    }
    
    public function headersProvider(): array
    {
        return [
            'JSONファイルにおけるcharsetパラメータ / Firefox' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0',
                ]),
                new JsonResponse(),
                ['content-type' => 'application/json; charset=UTF-8'],
            ],
            'JSONファイルにおけるcharsetパラメータ / Internet Explorer' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
                ]),
                new JsonResponse(),
                ['content-type' => 'application/json; charset=UTF-8'],
            ],
            'application/problem+json / Firefox' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0',
                ]),
                new JsonResponse([], Response::HTTP_BAD_REQUEST, ['content-type' => 'application/problem+json']),
                ['content-type' => 'application/json; charset=UTF-8'],
            ],
            'application/problem+json / Internet Explorer' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
                ]),
                new JsonResponse([], Response::HTTP_BAD_REQUEST, ['content-type' => 'application/problem+json']),
                ['content-type' => 'application/problem+json; charset=UTF-8'],
            ],
            'CSVファイルのインライン表示 / Firefox' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0',
                ]),
                new Response('', Response::HTTP_OK, [
                    'content-type' => 'text/csv; charset=UTF-8; header=absent',
                    'content-disposition' => 'inline',
                ]),
                ['content-type' => 'text/plain; charset=UTF-8'],
            ],
            'CSVファイルのインライン表示 / Internet Explorer' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
                ]),
                new Response('', Response::HTTP_OK, [
                    'content-type' => 'text/csv; charset=UTF-8; header=absent',
                    'content-disposition' => 'inline',
                ]),
                ['content-type' => 'text/plain; charset=UTF-8'],
            ],
            'CSVファイルのインライン表示 / content-dispositionヘッダが存在しない場合 / Firefox' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0',
                ]),
                new Response('', Response::HTTP_OK, [
                    'content-type' => 'text/csv; charset=UTF-8; header=absent',
                ]),
                ['content-type' => 'text/plain; charset=UTF-8'],
            ],
            'CSVファイルのインライン表示 / content-dispositionヘッダが存在しない場合 / Internet Explorer' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
                ]),
                new Response('', Response::HTTP_OK, [
                    'content-type' => 'text/csv; charset=UTF-8; header=absent',
                    'content-disposition' => 'inline',
                ]),
                ['content-type' => 'text/plain; charset=UTF-8'],
            ],
            'CSVファイルのダウンロード / Firefox' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0',
                ]),
                new Response('', Response::HTTP_OK, [
                    'content-type' => 'text/csv; charset=UTF-8; header=absent',
                    'content-disposition' => 'attachment; filename=dictionary.csv',
                ]),
                ['content-type' => 'text/csv; charset=UTF-8; header=absent'],
            ],
            'CSVファイルのダウンロード / Internet Explorer' => [
                new Request([], [], [], [], [], [
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko',
                ]),
                new Response('', Response::HTTP_OK, [
                    'content-type' => 'text/csv; charset=UTF-8; header=absent',
                    'content-disposition' => 'attachment; filename=dictionary.csv',
                ]),
                ['content-type' => 'text/csv; charset=UTF-8; header=absent'],
            ],
        ];
    }
}
