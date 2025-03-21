<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barber_id',
        'booking_date',
        'booking_time',
        'service_price',
        'deposit_amount',
        'balance_amount',
        'status',
        'payment_status',
        'notes'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'service_price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function transformation()
    {
        return $this->hasOne(Transformation::class);
    }

    public function calculateBalanceAmount()
    {
        return $this->service_price - $this->deposit_amount;
    }

    public function updateBalanceAmount()
    {
        $this->balance_amount = $this->calculateBalanceAmount();
        $this->save();
    }
}
