<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Corporation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'currency_id',
        'name',
        'owner',
        'tel_number',
        'gsm_number',
        'fax_number',
        'email',
        'address',
        'tax_office',
        'tax_number',
        'type',
    ];

    protected $appends = [
        'has_any_relation',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

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

    public function getHasAnyRelationAttribute()
    {
        return $this->agreements()->count() > 0 || $this->waybills()->count() > 0 || $this->invoices()->count() > 0 || $this->bills()->count() > 0 || $this->revenues()->count() > 0 || $this->expenses()->count() > 0;
    }

    public function getTotalFormalRevenueAttribute()
    {
        return $this->revenues()->where('company_id', session()->get('company_id'))->where('type', 'formal')->sum('amount');
    }

    public function getTotalInformalRevenueAttribute()
    {
        return $this->revenues()->where('company_id', session()->get('company_id'))->where('type', 'informal')->sum('amount');
    }

    public function getTotalFormalExpenseAttribute()
    {
        return $this->expenses()->where('company_id', session()->get('company_id'))->where('type', 'formal')->sum('amount');
    }

    public function getTotalInformalExpenseAttribute()
    {
        return $this->expenses()->where('company_id', session()->get('company_id'))->where('type', 'informal')->sum('amount');
    }

    public function getInvoiceTotalAttribute()
    {
        $invoices = $this->invoices()->where('company_id', session()->get('company_id'))->get();

        $total = 0;

        foreach ($invoices as $invoice) {
            $total += $invoice->total;
        }

        return $total;
    }

    public function getBillTotalAttribute()
    {
        $bills = $this->bills()->where('company_id', session()->get('company_id'))->get();

        $total = 0;

        foreach ($bills as $bill) {
            $total += $bill->total;
        }

        return $total;
    }
}
