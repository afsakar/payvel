<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'material_id',
        'quantity',
        'price',
    ];

    protected $appends = [
        'unit_name',
        'tax_rate',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function getTaxRateAttribute()
    {
        return $this->material->tax->rate;
    }

    public function getUnitNameAttribute()
    {
        return $this->material->unit->name;
    }
}
