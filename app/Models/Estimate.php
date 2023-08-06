<?php

namespace App\Models;

use App\Traits\HasUniqueCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estimate extends FinancialDocument
{
    use HasFactory, HasUniqueCode;

    protected $with = ['items', 'customer'];

    protected $appends = ['amount', 'total_amount'];

    protected $fillable = [
        'code',
        'user_id',
        'estimate_id',
        'customer_id',
        'date',
        'due_date',
        'tax_enabled',
        'currency',
        'notes',
        'description',
        'additional_charges',
        'more_discount',
        'status',
        'tax_percentage',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function scopeSearch($q, $query)
    {
        return $q->where('estimate_id', 'LIKE', '%' . $query . '%')
            ->orWhereHas('customer', fn ($qr) => $qr->where('name', 'LIKE', '%' . $query . '%'))
            ->orWhere('currency', 'LIKE', '%' . $query . '%')
            ->orWhere('notes', 'LIKE', '%' . $query . '%')
            ->orWhere('status', 'LIKE', '%' . $query . '%');
    }

    public static function modelName(): string
    {
        return 'estimate';
    }
}
