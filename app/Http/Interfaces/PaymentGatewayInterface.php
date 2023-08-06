<?php

namespace App\Http\Interfaces;

use App\Models\Plan;

interface PaymentGatewayInterface
{
  public function initializeSubscriptionAndGetPayLink(Plan $plan): string;
}
