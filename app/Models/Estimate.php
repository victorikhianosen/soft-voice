<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estimate extends Model
{
    use HasFactory;

    protected $with = ['items', 'customer'];

    protected $appends = ['amount'];

    protected $fillable = [
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
        'status'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
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
        if (!$this->attributes['more_discount']) {
            return [];
        }
        return json_decode($this->attributes['more_discount'], true);
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

    public function setEstimateIdAttribute(string $value)
    {
        $user = request()->user();
        $prefix = $user?->setting?->estimate_prefix ?? 'EST';
        $this->attributes['estimate_id'] = $prefix . $value;
    }

    public static function getNextEstimateId()
    {
        $user = request()->user();
        $prefix = $user->setting->estimate_prefix;
        $lastEstimate = $user->estimates()->latest()->first();
        if ($lastEstimate) {
            $id = (int) last(explode($prefix, $lastEstimate['estimate_id']));
            if ($id) {
                $newId = null;
                do {
                    $newId = str_pad(++$id, 6, '0', STR_PAD_LEFT);
                } while ($user->estimates()->where('estimate_id', $prefix . $newId)->exists());
                return $newId;
            }
        }
        return str_pad(1, 6, '0', STR_PAD_LEFT);
    }

    public function scopeSearch($q, $query)
    {
        return $q->where('estimate_id', 'LIKE', '%'.$query.'%')
            ->orWhereHas('customer', fn ($qr) => $qr->where('name', 'LIKE', '%'.$query.'%'))
            ->orWhere('currency', 'LIKE', '%'.$query.'%')
            ->orWhere('notes', 'LIKE', '%'.$query.'%')
            ->orWhere('status', 'LIKE', '%'.$query.'%');
    }
}
