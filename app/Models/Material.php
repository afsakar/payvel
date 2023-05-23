<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unit_id',
        'tax_id',
        'currency_id',
        'name',
        'description',
        'code',
        'price',
        'category',
        'type',
    ];

    protected $appends = [
        'has_any_relation',
    ];


    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function waybillItems()
    {
        return $this->hasMany(WaybillItem::class);
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->invoiceItems()->count() > 0 || $this->waybillItems()->count() > 0;
    }
}
