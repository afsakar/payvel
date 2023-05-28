<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'owner',
        'tel_number',
        'gsm_number',
        'fax_number',
        'email',
        'address',
        'tax_office',
        'tax_number',
        'logo',
    ];

    protected $appends = [
        'has_any_relation',
    ];

    public function agreements()
    {
        return $this->hasMany(Agreement::class);
    }

    public function waybills()
    {
        return $this->hasMany(Waybill::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
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

    public function getHasAnyRelationAttribute()
    {
        return $this->agreements->count() > 0 || $this->waybills->count() > 0 || $this->invoices->count() > 0 || $this->bills->count() > 0 || $this->revenues->count() > 0 || $this->expenses->count() > 0 || $this->checks->count() > 0;
    }
}
