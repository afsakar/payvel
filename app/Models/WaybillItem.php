<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaybillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'waybill_id',
        'material_id',
        'quantity',
        'price',
    ];

    protected $appends = [
        'tax_rate',
    ];

    public function waybill()
    {
        return $this->belongsTo(Waybill::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function getTaxRateAttribute()
    {
        return $this->material->tax->rate;
    }
}
