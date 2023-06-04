<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Waybill extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

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

    protected $appends = [
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

    public function items()
    {
        return $this->hasMany(WaybillItem::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->invoices()->count() > 0 || $this->bills()->count() > 0;
    }
}
