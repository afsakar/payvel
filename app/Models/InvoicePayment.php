<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'revenue_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function revenue()
    {
        return $this->belongsTo(Revenue::class);
    }
}
