<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class FinancialDocument extends Model
{
	abstract protected static function modelName(): string;

	public function customer(): BelongsTo
	{
		return $this->belongsTo(Customer::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
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

	public function getAmountAttribute(): float|int
	{
		return $this->items()->sum('amount');
	}

	public static function nextId()
	{
		$user = request()->user();
		$modelName = static::modelName();
		$prefix = $user->setting->{$modelName.'_prefix'};
		$count = static::where('user_id', $user->id)->count();

		$newId = null;
		do {
			$newId = $prefix . str_pad(++$count, 6, '0', STR_PAD_LEFT);
		} while (static::where('user_id', $user->id)->where($modelName.'_id', $newId)->exists());
		return trim($newId);
	}

	public function getTotalAmountAttribute()
	{
		$itemsAmount = 0;

		foreach ($this->items as $item) {
			$itemsAmount += $item->amount;
			if ($item->discount > 0) {
				$itemsAmount -= ($item->discount / 100) * $item->amount;
			}
		}

		$totalAmount = $itemsAmount;

		foreach ($this->additional_charges as $charge) {
			if ($charge['mode'] === 'percent') {
				$totalAmount += ($charge['value'] / 100) * $itemsAmount;
			} else {
				$totalAmount += $charge['value'];
			}
		}

		foreach ($this->more_discount as $discount) {
			if ($discount['mode'] === 'percent') {
				$totalAmount -= ($discount['value'] / 100) * $itemsAmount;
			} else {
				$totalAmount -= $discount['value'];
			}
		}

		if ($this->tax_enabled) {
			$taxPercentage = $this->tax_percentage;
			$totalAmount += ($taxPercentage / 100) * $totalAmount;
		}

		return max(0, $totalAmount);
	}

	protected function extractJSONField($data): string
	{
		return json_encode(
			Arr::map(
				$data,
				fn ($item) => Arr::only($item, ['description', 'mode', 'value'])
			)
		);
	}
}
