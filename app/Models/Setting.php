<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency',
        'auto_payment_reminder',
        'invoice_prefix',
        'estimate_prefix',
        'service_category',
        'signature',
        'signatory_name',
        'preferred_due_date',
        'payment_history_on_invoice_page',
        'auto_enable_tax',
        'auto_enable_bank_account',
        'preferred_bank_id',
        'tax_percentage',
        'theme',
        'timezone',
        'date_format',
        'financial_year',
        'notifications',
        'business_logo',
    ];

    public function setNotificationsAttribute(array $value)
    {
        $this->attributes['notifications'] = json_encode($value);
    }

    public function getNotificationsAttribute()
    {
        return json_decode($this->attributes['notifications']);
    }
}
