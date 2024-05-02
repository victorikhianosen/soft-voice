<?php

return [
  'paystack' => [
    'base_url'   => env('PAYSTACK_BASE_URL'),
    'public_key' => env('PAYSTACK_PUBLIC_KEY'),
    'secret_key' => env('PAYSTACK_SECRET_KEY'),
  ],
  'flutterwave' => [
    'base_url'   => env('FLUTTERWAVE_BASE_URL'),
    'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
    'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
    'webhoook_signature' => env('FLUTTERWAVE_WEBHOOK_SIGNATURE'),
  ],
  'stripe' => [
    'base_url'   => env('STRIPE_BASE_URL'),
    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'webhook_id' => env('STRIPE_WEBHOOK_ID'),
  ],
  'paypal' => [
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'client_secret' => env('PAYPAL_CLIENT_SECRET'),
    'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    'base_url' => env('PAYPAL_BASE_URL'),
  ],
];
