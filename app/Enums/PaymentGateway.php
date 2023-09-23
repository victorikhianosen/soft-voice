<?php

namespace App\Enums;

enum PaymentGateway: string
{
	case PAYSTACK = 'paystack';
	case FLUTTERWAVE = 'flutterwave';
	case STRIPE = 'stripe';
	case PAYPAL = 'paypal';

	public static function values()
	{
		return [
			self::PAYSTACK->value,
			self::FLUTTERWAVE->value,
			self::STRIPE->value,
			self::PAYPAL->value,
		];
	}


	public static function usd()
	{
		return [
			self::STRIPE->value,
			self::PAYPAL->value,
		];
	}

	public static function ngn()
	{
		return [
			self::PAYSTACK->value,
			self::FLUTTERWAVE->value,
		];
	}
}
