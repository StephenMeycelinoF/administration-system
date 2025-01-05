<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'npwp',
        'name',
        'address',
        'phone',
        'email',
        'province_id',
        'regency_id',
        'district_id',
        'village_id'
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function slips()
    {
        return $this->hasMany(Slip::class, 'customer_id');
    }
}
