<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

/**
 * アップロードされた個々のファイルにエラーがないか確認します。
 */
class VerifyUploadFileWithoutError
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\UploadException
     */
    public function handle(Request $request, Closure $next): Response
    {
        foreach (array_flatten($request->allFiles()) as $file) {
            if ($file && !$file->isValid()) {
                throw new UploadException($file->getErrorMessage(), $file->getError());
            }
        }
        
        return $next($request);
    }
}
