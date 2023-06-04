<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

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

    protected $appends = [
        'invoice_payments_sum',
        'has_any_relation',
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

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function getInvoicePaymentsSumAttribute()
    {
        $revenues = $this->payments->pluck('revenue_id')->toArray();

        $sum = 0;

        foreach ($revenues as $revenue) {
            $sum += Revenue::find($revenue)->amount;
        }

        return $sum;
    }

    public function getTotalAttribute()
    {
        if ($this->items->isEmpty()) {
            $sum = $this->waybill->items->sum(function ($item) {
                return $item->quantity * $item->material->price;
            });

            $totalTax = $this->waybill->items->sum(function ($item) {
                return $item->quantity * $item->material->price * $item->tax_rate / 100;
            });

            return $sum + $totalTax - $this->discount - ($totalTax * $this->with_holding->rate / 100);
        } else {

            $sum = $this->items->sum(function ($item) {
                return $item->quantity * $item->material->price;
            });

            $totalTax = $this->items->sum(function ($item) {
                return $item->quantity * $item->material->price * $item->tax_rate / 100;
            });

            return $sum + $totalTax - $this->discount - ($totalTax * $this->with_holding->rate / 100);
        }
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->payments->count() > 0;
    }
}
