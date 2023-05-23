<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'company_id',
        'corporation_id',
        'category_id',
        'description',
        'amount',
        'type',
        'due_at'
    ];

    protected $casts = [
        'due_at' => 'date'
    ];

    protected $appends = [
        'has_any_relation',
        'invoice_number'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_payments');
    }

    public function getHasAnyRelationAttribute()
    {
        return $this->invoices()->count() > 0;
    }

    public function getInvoiceNumberAttribute()
    {
        return $this->invoices()->first()->number ?? null;
    }
}
