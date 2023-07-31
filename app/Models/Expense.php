<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'date',
        'recurring',
        'recurring_duration',
        'amount',
        'notes',
        'attachment',
        'currency',
        'status'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function setRecurringDurationAttribute(array $data)
    {
        $this->attributes['recurring_duration'] = json_encode(Arr::only($data, ['mode', 'value']));
    }

    public function getRecurringDurationAttribute(): array
    {
        return json_decode($this->attributes['recurring_duration'], true) ?? [];
    }

    public function scopeSearch($q, $query)
    {
        return $q->where('amount', 'LIKE', '%'.$query.'%')
            ->orWhereHas('customer', fn ($qr) => $qr->where('name', 'LIKE', '%'.$query.'%'))
            ->orWhere('notes', 'LIKE', '%'.$query.'%')
            ->orWhere('status', 'LIKE', '%'.$query.'%');
    }
}
