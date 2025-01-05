<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slip extends Model
{

    protected $fillable = [
        'slip_code',
        'plat_number',
        'driver_name',
        'delivery_order',
        'bruto_muat',
        'tara_muat',
        'bruto_bongkar',
        'tara_bongkar',
        'date_slip',
        'customer_id',
        'invoice_number',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    protected static function booted()
    {
        static::creating(function ($slip) {
            $lastSlip = Slip::latest('id')->first();
            $lastSlipCode = $lastSlip ? $lastSlip->slip_code : null;

            if (!$lastSlipCode) {
                $slip->slip_code = 'SLP-001';
            } else {
                $lastNumber = (int)substr($lastSlipCode, 4);
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                $slip->slip_code = 'SLP-' . $newNumber;
            }
        });
    }
}
