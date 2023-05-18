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

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
