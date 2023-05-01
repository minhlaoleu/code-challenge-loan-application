<?php

namespace App\Exceptions;

use App\Logs\Logger;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        /**
         * In case the flag debug is turned off, we should handle the exception gracefully
         */
        if ( !env('APP_DEBUG', true) )
        {
            /**
             * handle 404 error
             */
            if ($exception instanceof NotFoundHttpException)
            {
                return response()->json([
                   'error' => 'Not Found'
                ])->setStatusCode(Response::HTTP_NOT_FOUND);
            }

            /*
             * handle 500 error
             */
            $exceptionDetailReport = "Exception (get_class({$exception})) : " . $exception->getTraceAsString();
            $error = (new Logger())->log('error', $exceptionDetailReport, true);
            return response()->json([
                'error' => "Server issue found, code[{$error}]"
            ])->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return parent::render($request, $exception);
    }
}
