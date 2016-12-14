<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * セッションに「_status」キーが含まれていれば、HTTPステータスコードをその値に変更します。
 */
class HTTPStatus
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
        if (session()->has('_status')) {
            $response->setStatusCode(session('_status'));
        }
        return $response;
    }
}
