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

    public function getHasAnyRelationAttribute()
    {
        return $this->agreements()->count() > 0;
    }
}
