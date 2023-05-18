<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Waybill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'corporation_id',
        'number',
        'address',
        'status',
        'due_date',
        'waybill_date',
        'content',
    ];

    protected $casts = [
        'due_date' => 'date',
        'waybill_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    public function items()
    {
        return $this->hasMany(WaybillItem::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
