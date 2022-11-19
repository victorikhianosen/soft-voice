<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'additional_charges',
        'more_discount',
        'signature',
        'signatory_name',
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
        return json_decode($this->attributes['recurring_duration'], true);
    }

    public function setAdditionalChargesAttribute(array $data)
    {
        $this->attributes['additional_charges'] = $this->extractJSONField($data);
    }

    public function getAdditionalChargesAttribute(): array
    {
        return json_decode($this->attributes['additional_charges'], true);
    }

    public function setMoreDiscountAttribute(array $data)
    {
        $this->attributes['more_discount'] = $this->extractJSONField($data);
    }

    public function getMoreDiscountAttribute(): array
    {
        return json_decode($this->attributes['more_discount'], true);
    }

    private function extractJSONField($data): string
    {
        return json_encode(Arr::map(
            $data,
            fn($item) => Arr::only($item, ['description', 'mode', 'value']))
        );
    }
}
