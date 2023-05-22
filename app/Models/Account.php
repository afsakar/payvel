<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_type_id',
        'currency_id',
        'name',
        'description',
        'starting_balance',
    ];

    protected $appends = [
        'balance',
    ];

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    public function getBalanceAttribute()
    {
        return $this->starting_balance + $this->revenues()->sum('amount');
    }
}
