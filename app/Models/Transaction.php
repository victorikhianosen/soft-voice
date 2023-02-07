<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeSearch($q, $query)
    {
        return $q->where('amount', 'LIKE', '%'.$query.'%')
            ->orWhere('description', 'LIKE', '%'.$query.'%')
            ->orWhere('status', 'LIKE', '%'.$query.'%')
            ->orWhere('type', 'LIKE', '%'.$query.'%');
    }
}
