<?php

namespace App\Exceptions;

use Throwable;
use App\Http\Traits\RespondWithHttpStatus;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use RespondWithHttpStatus;
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
            //
        });
    }

    public function render($request, Throwable $exception)
	{
		if ($request->expectsJson()){
            if ($exception instanceof ModelNotFoundException){
                return $this->failure('Model not found', 'the specied model could not be found.', 404);
            }
            if ($exception instanceof MethodNotAllowedHttpException){
                return $this->failure('Method not allowed', 'the specified method is not allowed.', 405);
            }
            if ($exception instanceof NotFoundHttpException){
                return $this->failure('Invalid route', 'the specified route is invalid.', 404);
            }
            if ($exception instanceof ThrottleRequestsException){
                return $this->failure('Too many request', 'too many requests, try again later.', 429);
            }
            if ($exception instanceof AccessDeniedHttpException || $exception instanceof AuthorizationException) {
                return $this->failure('Unauthorized', 'this action is unauthorized.', 403);
            }
            if ($exception instanceof ConnectionException) {
                return $this->failure('Bad request', 'an error was encountered', 400);
            }
            if ($exception instanceof HttpException) {
                return $this->failure($exception->getMessage(), $exception->getCode());
            }
        }
		return parent::render($request, $exception);
	}
}
