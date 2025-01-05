<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['name', 'type', 'code', 'balance'];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
