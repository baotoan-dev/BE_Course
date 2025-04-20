<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Custom reporting logic, if needed
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('api/*')) {
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'statusCode' => 422,
                    'key' => 'validation_error',
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors(),
                ], 422);
            }

            if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                return response()->json([
                    'statusCode' => $exception->getStatusCode(),
                    'key' => 'error',
                    'message' => $exception->getMessage(),
                ], $exception->getStatusCode());
            }

            return response()->json([
                'statusCode' => 500,
                'key' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
