<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'company_id',
        'corporation_id',
        'category_id',
        'description',
        'amount',
        'type',
        'due_at'
    ];

    protected $casts = [
        'due_at' => 'date'
    ];

    protected $appends = [
        'has_any_relation',
        'bill_number',
        'transaction_type',
        'amount_with_currency'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    public function bills()
    {
        return $this->belongsToMany(Bill::class, 'bill_payments');
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->bills()->count() > 0;
    }

    public function getBillNumberAttribute()
    {
        return $this->bills()->first()->number ?? null;
    }

    public function getAmountWithCurrencyAttribute()
    {
        if ($this->corporation->currency->position === "right") {
            return number_format($this->amount, 2) . " " . $this->corporation->currency->symbol;
        } else {
            return $this->corporation->currency->symbol . " " . number_format($this->amount, 2);
        }
    }
}
