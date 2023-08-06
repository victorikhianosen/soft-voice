<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Traits\RespondWithHttpStatus;

class CustomerException extends Exception
{
    use RespondWithHttpStatus;

    public function render($request)
	{
        return $this->failure($this->getMessage(), status: $this->getCode());
	}
}
