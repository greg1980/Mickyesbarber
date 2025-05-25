<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barber extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'bio',
        'is_approved',
        'availability',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_approved' => 'boolean',
        'availability' => 'boolean',
    ];

    /**
     * Get the user that owns the barber profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function availabilitySchedules()
    {
        return $this->hasMany(BarberAvailabilitySchedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'barber_id');
    }
}
