<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
        'has_any_relation',
        'paid_sale_checks',
        'paid_purchase_checks',
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

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function outgoingTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    public function incomingTransactions()
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    public function getBalanceAttribute()
    {
        $balance = $this->starting_balance + $this->revenues()->sum('amount') - $this->expenses()->sum('amount') + $this->incomingTransactions()->sum('amount') - $this->outgoingTransactions()->sum('amount') - $this->paidSaleChecks()->sum('amount') + $this->paidPurchaseChecks()->sum('amount');

        if ($this->currency->position == 'right') {
            return number_format($balance, 2) . ' ' . $this->currency->symbol;
        } else {
            return $this->currency->symbol . ' ' . number_format($balance, 2);
        }
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->revenues()->count() > 0 || $this->expenses()->count() > 0 || $this->incomingTransactions()->count() > 0 || $this->outgoingTransactions()->count() > 0 || $this->checks()->count() > 0;
    }

    public function checks()
    {
        return $this->hasMany(Check::class);
    }

    public function purchaseChecks()
    {
        return $this->checks()->where('type', 'purchase');
    }

    public function saleChecks()
    {
        return $this->checks()->where('type', 'sale');
    }

    public function paidSaleChecks()
    {
        return $this->saleChecks()->where('status', 'paid');
    }

    public function paidPurchaseChecks()
    {
        return $this->purchaseChecks()->where('status', 'paid');
    }

    public function getPaidSaleChecksAttribute()
    {
        return $this->paidSaleChecks()->sum('amount');
    }

    public function getPaidPurchaseChecksAttribute()
    {
        return $this->paidPurchaseChecks()->sum('amount');
    }
}
