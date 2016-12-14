<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Exception\PostTooLargeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use bantu\IniGetWrapper\IniGetWrapper;
use ScriptFUSION\Byte\ByteFormatter;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof TokenMismatchException) {
            return back(Response::HTTP_SEE_OTHER)->with('_status', Response::HTTP_FORBIDDEN)
                ->withErrors(_('セッション切れです。もう一度送信をお願いします。'))->withInput();
        } elseif ($e instanceof PostTooLargeException) {
            $byteFormatter = new ByteFormatter();
            return back(Response::HTTP_SEE_OTHER)->with('_status', Response::HTTP_REQUEST_ENTITY_TOO_LARGE)
                ->withErrors(sprintf(
                    _('送信データの容量が %1$s あります。送信データ全体の容量は %2$s 以内にしてください。'),
                    $byteFormatter->format($request->server('CONTENT_LENGTH')),
                    $byteFormatter->format((new IniGetWrapper())->getBytes('post_max_size'))
                ));
        } elseif ($e instanceof UploadException) {
            switch ($e->getCode()) {
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
            return back(Response::HTTP_SEE_OTHER)->with('_status', $status)->withErrors($e->getMessage())->withInput();
        }
        return parent::render($request, $e);
    }
}
