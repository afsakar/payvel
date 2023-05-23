<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'position',
    ];

    public function corporations()
    {
        return $this->hasMany(Corporation::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->corporations->count() > 0 || $this->accounts->count() > 0;
    }
}
