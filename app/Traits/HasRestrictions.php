<?php

namespace App\Traits;

use App\Models\Restriction;
use App\Models\User;
use Illuminate\Support\Arr;

trait HasRestrictions
{
  public function hasExhaustedCount(string $type)
  {
    $restriction = $this->getRestriction($type);

    if ($restriction['pivot']['value'] == -1) {
      return false;
    }

    $count = request()->user()->{$type}()
          ->whereDate('created_at', '>=', now()->startOfMonth())
          ->whereDate('created_at', '<=', now()->endOfMonth())
          ->count();

    return $count >= $restriction['pivot']['value'];
  }

  public function isRestricted(string $type, User|null $user = null)
  {
    $restriction = $this->getRestriction($type, $user);
    return $restriction['pivot']['value'] === 0;
  }

  private function getRestriction(string $type, User|null $user = null): ?Restriction
  {
    $user = $user ?? request()->user();
    $restrictions = $user->active_subscription->plan->restrictions;
    $restriction = Arr::first($restrictions, fn ($r) => $r['name'] == $type);

    if (!$restriction) {
      abort(400, "Restriction of type {$type} not found");
    }

    return $restriction;
  }
}
