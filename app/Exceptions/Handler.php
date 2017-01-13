<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Exception\PostTooLargeException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use bantu\IniGetWrapper\IniGetWrapper;
use ScriptFUSION\Byte\ByteFormatter;
use DOMDocument;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            return back(Response::HTTP_SEE_OTHER)->with('_status', Response::HTTP_FORBIDDEN)
                ->withErrors(_('セッション切れです。もう一度送信をお願いします。'))->withInput();
        } elseif ($exception instanceof PostTooLargeException) {
            $byteFormatter = new ByteFormatter();
            return back(Response::HTTP_SEE_OTHER)->with('_status', Response::HTTP_REQUEST_ENTITY_TOO_LARGE)
                ->withErrors(sprintf(
                    _('送信データの容量が %1$s あります。送信データ全体の容量は %2$s 以内にしてください。'),
                    $byteFormatter->format($request->server('CONTENT_LENGTH')),
                    $byteFormatter->format((new IniGetWrapper())->getBytes('post_max_size'))
                ));
        } elseif ($exception instanceof UploadException) {
            switch ($exception->getCode()) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $status = Response::HTTP_REQUEST_ENTITY_TOO_LARGE;
                    break;
                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                    $status = Response::HTTP_BAD_REQUEST;
                    break;
                default:
                    $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
            return back(Response::HTTP_SEE_OTHER)
                ->with('_status', $status)->withErrors($exception->getMessage())->withInput();
        }
        return parent::render($request, $exception);
    }
    
    /**
     * @inheritDoc
     */
    protected function convertExceptionToResponse(Exception $e): Response
    {
        $response = parent::convertExceptionToResponse($e);
        
        $doc = new DOMDocument();
        $doc->loadHTML($response->getContent());
        $link = $doc->createElement('link');
        $link->setAttribute('href', asset('css/symfony-debug-exception.css'));
        $link->setAttribute('rel', 'stylesheet');
        $style = $doc->getElementsByTagName('style')->item(0);
        $style->parentNode->replaceChild($link, $style);
        $response->setContent($doc->saveHTML());
        
        return $response;
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest('login');
    }
}
