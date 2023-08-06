<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUniqueCode
{
	public static function getUniqueCode()
	{
		$code = null;
		do {
			$code = Str::random(10);
		} while (static::where('code', $code)->exists());
		return $code;
	}
}
