<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'rate',
    ];

    protected $appends = [
        'has_any_relation'
    ];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->materials()->count() > 0;
    }
}
