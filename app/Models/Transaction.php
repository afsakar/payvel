<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['from_account_id', 'to_account_id', 'amount', 'description', 'due_at'];

    protected $appends = [
        'transaction_type',
        'amount_with_currency'
    ];

    protected $casts = [
        'due_at' => 'date'
    ];


    public function from_account()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function to_account()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function getTransactionTypeAttribute()
    {
        return $this->from_account_id || $this->to_account_id ? 'transaction' : '';
    }

    public function getAmountWithCurrencyAttribute()
    {
        if ($this->from_account->currency->position === "right") {
            return number_format($this->amount, 2) . " " . $this->from_account->currency->symbol;
        } else {
            return $this->from_account->currency->symbol . " " . number_format($this->amount, 2);
        }
    }
}
