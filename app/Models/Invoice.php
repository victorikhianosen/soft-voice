<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $with = ['items', 'customer'];

    protected $appends = ['amount', 'amount_paid', 'amount_due'];

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

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function setAdditionalChargesAttribute(array $data)
    {
        $this->attributes['additional_charges'] = $this->extractJSONField($data);
    }

    public function getAdditionalChargesAttribute(): array
    {
        return json_decode($this->attributes['additional_charges'], true) ?? [];
    }

    public function setMoreDiscountAttribute(array $data)
    {
        $this->attributes['more_discount'] = $this->extractJSONField($data);
    }

    public function getMoreDiscountAttribute(): array
    {
        return json_decode($this->attributes['more_discount'], true) ?? [];
    }

    private function extractJSONField($data): string
    {
        return json_encode(Arr::map(
            $data,
            fn($item) => Arr::only($item, ['description', 'mode', 'value']))
        );
    }

    public function getAmountAttribute(): float|int
    {
        return $this->items()->sum('amount');
    }

    public function getAmountPaidAttribute(): float|int
    {
        return $this->payments()->sum('amount');
    }

    public function getAmountDueAttribute(): float|int
    {
        return max(0, $this->amount - $this->amount_paid);
    }

    public static function getNextInvoiceId()
    {
        $user = request()->user();
        $prefix = $user->setting->invoice_prefix;
        $invoicesCount = $user->invoices()->count();

        $newId = null;
        do {
            $newId = $prefix . str_pad(++$invoicesCount, 6, '0', STR_PAD_LEFT);
        } while ($user->invoices()->where('invoice_id', $newId)->exists());
        return $newId;
    }

    public static function getUniqueCode()
    {
        $code = null;
        do {
            $code = Str::random(10);
        } while (Invoice::where('code', $code)->exists());
        return $code;
    }

    public function scopeSearch($q, $query)
    {
        return $q->where('invoice_id', 'LIKE', '%'.$query.'%')
            ->orWhereHas('customer', fn ($qr) => $qr->where('name', 'LIKE', '%'.$query.'%'))
            ->orWhere('currency', 'LIKE', '%'.$query.'%')
            ->orWhere('terms', 'LIKE', '%'.$query.'%')
            ->orWhere('notes', 'LIKE', '%'.$query.'%')
            ->orWhere('signatory_name', 'LIKE', '%'.$query.'%')
            ->orWhere('status', 'LIKE', '%'.$query.'%');
    }
}
