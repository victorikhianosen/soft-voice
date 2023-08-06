<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Traits\RespondWithHttpStatus;
use Illuminate\Http\Request;

class AuthException extends Exception
{
    use RespondWithHttpStatus;

    public function render(Request $request)
	{
        return $this->failure($this->message, status: $this->code);
	}
}
