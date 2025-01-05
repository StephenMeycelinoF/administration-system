<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'transport_cost',
        'slip_id',
        'customer_id',
        'due_date',
        'total_amount',
        'total_dpp',
        'ppn',
        'pph_23',
        'status',
        'created_by'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function slip(): BelongsTo
    {
        return $this->belongsTo(Slip::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
