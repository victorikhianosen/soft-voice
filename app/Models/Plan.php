<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function restrictions(): BelongsToMany
    {
        return $this->belongsToMany(Restriction::class)
                    ->withPivot('value')
                    ->withTimestamps();
    }
}
