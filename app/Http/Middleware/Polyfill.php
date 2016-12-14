<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\{Response, ResponseHeaderBag};

class Polyfill
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $this->fixMojibakeForJSONOnBlink($request, $response);
        $this->fixNotFoundForProblemJSONOnFirefox($request, $response);
        $this->fixInlineCSV($request, $response);
        return $response;
    }
    
    /**
     * JSONファイルを直接表示した際に文字化けする不具合に対処します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    protected function fixMojibakeForJSONOnBlink(Request $request, Response  $response): void
    {
        $contentType = $response->headers->get('content-type');
        if (preg_match('#^application/(?:.+\\+)?json#i', $contentType)) {
            $response->headers->set('content-type', "$contentType; charset=UTF-8");
        }
    }
    
    /**
     * Firefox において、問題詳細JSONファイルを直接表示しようとした際に「ファイルが見つかりませんでした」と表示される不具合に対処します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    protected function fixNotFoundForProblemJSONOnFirefox(Request $request, Response  $response): void
    {
        if (str_contains($request->header('user-agent'), 'Firefox/')) {
            $contentType = explode(';', $response->headers->get('content-type'), 2);
            if ($contentType[0] === 'application/problem+json') {
                $response->headers->set('content-type', 'application/json' . (isset($contentType[1]) ? ";$contentType[1]" : ''));
            }
        }
    }
    
    /**
     * CSVファイルをブラウザ内で表示できない問題に対処します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    protected function fixInlineCSV(Request $request, Response  $response): void
    {
        if (preg_match(
            '#^text/csv(?:.*(;\\s*charset=[-0-9:_a-z]+))?#i',
            $response->headers->get('content-type'),
            $matches
        ) && (
            !$response->headers->has('content-disposition')
            || starts_with($response->headers->get('content-disposition'), ResponseHeaderBag::DISPOSITION_INLINE)
        )) {
            $response->headers->set('content-type', 'text/plain' . ($matches[1] ?? ''));
        }
    }
}
