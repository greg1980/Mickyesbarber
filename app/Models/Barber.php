<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'bio',
        'profile_photo',
        'years_of_experience',
        'specialties',
        'is_available'
    ];

    protected $casts = [
        'specialties' => 'array',
        'is_available' => 'boolean'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function transformations()
    {
        return $this->hasMany(Transformation::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->transformations()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->transformations()->whereNotNull('rating')->count();
    }
}
