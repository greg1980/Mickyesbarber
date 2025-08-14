<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BusinessHours extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_open',
        'display_text',
        'sort_order'
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
    ];

    /**
     * Get all business hours ordered by sort order
     */
    public static function getOrderedHours()
    {
        return self::orderBy('sort_order')->get();
    }

    /**
     * Get formatted display text for a day
     */
    public function getFormattedDisplayText()
    {
        if (!$this->is_open) {
            return 'Closed';
        }

        if ($this->open_time && $this->close_time) {
            $open = Carbon::parse($this->open_time)->format('g:i A');
            $close = Carbon::parse($this->close_time)->format('g:i A');
            return "{$open} - {$close}";
        }

        return $this->display_text ?? 'Hours TBD';
    }

    /**
     * Check if business is currently open
     */
    public static function isCurrentlyOpen()
    {
        $today = strtolower(now()->format('l')); // monday, tuesday, etc.
        $businessHour = self::where('day_of_week', $today)->first();

        if (!$businessHour || !$businessHour->is_open) {
            return false;
        }

        $now = now();
        $openTime = Carbon::parse($businessHour->open_time);
        $closeTime = Carbon::parse($businessHour->close_time);

        return $now->between($openTime, $closeTime);
    }

    /**
     * Get next opening time
     */
    public static function getNextOpeningTime()
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $today = strtolower(now()->format('l'));
        $todayIndex = array_search($today, $days);

        // Check remaining days this week
        for ($i = 0; $i < 7; $i++) {
            $checkIndex = ($todayIndex + $i) % 7;
            $checkDay = $days[$checkIndex];

            $businessHour = self::where('day_of_week', $checkDay)->first();

            if ($businessHour && $businessHour->is_open) {
                $nextDate = now()->addDays($i);
                $openTime = Carbon::parse($businessHour->open_time);

                return [
                    'day' => ucfirst($checkDay),
                    'date' => $nextDate->format('l, M j'),
                    'time' => $openTime->format('g:i A')
                ];
            }
        }

        return null;
    }
}
