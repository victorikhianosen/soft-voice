<?php

namespace App\Models;

use Illuminate\Support\Arr;
use App\Traits\HasUniqueCode;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends FinancialDocument
{
    use HasFactory, HasUniqueCode;

    protected $with = ['items', 'customer'];

    protected $appends = ['amount', 'amount_paid', 'amount_due', 'total_amount'];

    protected $fillable = [
        'code',
        'user_id',
        'invoice_id',
        'customer_id',
        'date',
        'due_date',
        'tax_enabled',
        'recurring',
        'accept_payment',
        'currency',
        'recurring_duration',
        'terms',
        'notes',
        'description',
        'additional_charges',
        'more_discount',
        'signature',
        'signatory_name',
        'status'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function setRecurringDurationAttribute(array $data)
    {
        $this->attributes['recurring_duration'] = json_encode(Arr::only($data, ['mode', 'value']));
    }

    public function getRecurringDurationAttribute(): array
    {
        return json_decode($this->attributes['recurring_duration'], true) ?? [];
    }

    public function getAmountPaidAttribute(): float|int
    {
        return $this->payments()->sum('amount');
    }

    public function getAmountDueAttribute(): float|int
    {
        return max(0, $this->amount - $this->amount_paid);
    }


    public function scopeSearch($q, $query)
    {
        return $q->where('invoice_id', 'LIKE', '%' . $query . '%')
            ->orWhereHas('customer', fn ($qr) => $qr->where('name', 'LIKE', '%' . $query . '%'))
            ->orWhere('currency', 'LIKE', '%' . $query . '%')
            ->orWhere('terms', 'LIKE', '%' . $query . '%')
            ->orWhere('notes', 'LIKE', '%' . $query . '%')
            ->orWhere('signatory_name', 'LIKE', '%' . $query . '%')
            ->orWhere('status', 'LIKE', '%' . $query . '%');
    }

    public static function getPrefix()
    {
        return request()->user()->setting->invoice_prefix;
    }
}
