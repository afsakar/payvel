<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Check extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'account_id',
        'company_id',
        'corporation_id',
        'number',
        'amount',
        'description',
        'issue_date',
        'due_date',
        'paid_date',
        'type',
        'status',
        'image',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function corporation()
    {
        return $this->belongsTo(Corporation::class);
    }

    public function getAmountWithCurrencyAttribute()
    {
        if ($this->corporation->currency->position === "right") {
            return number_format($this->amount, 2) . " " . $this->corporation->currency->symbol;
        } else {
            return $this->corporation->currency->symbol . " " . number_format($this->amount, 2);
        }
    }
}
