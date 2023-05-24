<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'expense_id',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
