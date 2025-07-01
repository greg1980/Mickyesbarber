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
        'service_id',
        'booking_date',
        'booking_time',
        'service_price',
        'status',
        'amount_paid',
        'deposit_paid',
        'payment_status',
        'stripe_payment_id'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'service_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'deposit_paid' => 'boolean',
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

    public function service()
    {
        return $this->belongsTo(\App\Models\Service::class, 'service_id');
    }

    public function calculateBalanceAmount()
    {
        return $this->service_price - $this->amount_paid;
    }

    public function updateBalanceAmount()
    {
        // No longer needed, but kept for compatibility
        // $this->balance_amount = $this->calculateBalanceAmount();
        // $this->save();
    }
}
