<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Restriction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class)->withTimestamps();
    }
}
