<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['from_account_id', 'to_account_id', 'amount', 'description', 'due_at'];

    public function from_account()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function to_account()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }
}
