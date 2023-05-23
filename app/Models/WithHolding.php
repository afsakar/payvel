<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithHolding extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'rate',
    ];

    protected $appends = [
        'has_any_relation'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->invoices()->count() > 0;
    }
}
