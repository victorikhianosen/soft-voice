<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Http\Traits\RespondWithHttpStatus;

class CustomerException extends Exception
{
    use RespondWithHttpStatus;

    public function render($request, Throwable $exception)
	{
        return $this->failure($exception->getMessage(), status: $exception->getCode());
	}
}
