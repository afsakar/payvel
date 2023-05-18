<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'corporation_id',
        'waybill_id',
        'with_holding_id',
        'number',
        'status',
        'issue_date',
        'discount',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    public function waybill()
    {
        return $this->belongsTo(Waybill::class);
    }

    public function with_holding()
    {
        return $this->belongsTo(WithHolding::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getTotalAttribute()
    {
        if ($this->items->isEmpty()) {
            $sum = $this->waybill->items->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            $totalTax = $this->waybill->items->sum(function ($item) {
                return $item->quantity * $item->price * $item->tax_rate / 100;
            });

            return $total = $sum + $totalTax - $this->discount - ($totalTax * $this->with_holding->rate / 100);
        } else {


            $sum = $this->items->sum(function ($item) {
                return $item->quantity * $item->price;
            });

            $totalTax = $this->items->sum(function ($item) {
                return $item->quantity * $item->price * $item->tax_rate / 100;
            });

            $total = $sum + $totalTax - $this->discount - ($totalTax * $this->with_holding->rate / 100);

            $totalWithCurrency = $this->corporation->currency->position == "before" ? $this->corporation->currency->symbol . ' ' . number_format($total, 2) : number_format($total, 2) . ' ' . $this->corporation->currency->symbol;

            return $total;
        }
    }
}
