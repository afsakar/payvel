<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $appends = [
        'has_any_relation',
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->accounts->count() > 0;
    }
}
