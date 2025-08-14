<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessHours;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminBusinessHoursController extends Controller
{
    /**
     * Display the business hours management page
     */
    public function index()
    {
        $businessHours = BusinessHours::orderBy('sort_order')->get();

        return Inertia::render('Admin/BusinessHours', [
            'businessHours' => $businessHours
        ]);
    }

    /**
     * Get all business hours for API
     */
    public function getAll()
    {
        $businessHours = BusinessHours::orderBy('sort_order')->get();

        return response()->json([
            'businessHours' => $businessHours
        ]);
    }

    /**
     * Update business hours
     */
    public function update(Request $request)
    {
        $request->validate([
            'businessHours' => 'required|array',
            'businessHours.*.id' => 'required|exists:business_hours,id',
            'businessHours.*.day_of_week' => 'required|string',
            'businessHours.*.open_time' => 'nullable|date_format:H:i',
            'businessHours.*.close_time' => 'nullable|date_format:H:i',
            'businessHours.*.is_open' => 'required|boolean',
            'businessHours.*.display_text' => 'nullable|string|max:100',
            'businessHours.*.sort_order' => 'required|integer|min:0',
        ]);

        try {
            foreach ($request->businessHours as $hourData) {
                $businessHour = BusinessHours::find($hourData['id']);

                if ($businessHour) {
                    $businessHour->update([
                        'open_time' => $hourData['is_open'] && $hourData['open_time'] ? $hourData['open_time'] : null,
                        'close_time' => $hourData['is_open'] && $hourData['close_time'] ? $hourData['close_time'] : null,
                        'is_open' => $hourData['is_open'],
                        'display_text' => $hourData['display_text'],
                        'sort_order' => $hourData['sort_order'],
                    ]);
                }
            }

            return response()->json([
                'message' => 'Business hours updated successfully',
                'businessHours' => BusinessHours::orderBy('sort_order')->get()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update business hours: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset business hours to default values
     */
    public function resetToDefault()
    {
        try {
            // Delete existing hours
            BusinessHours::truncate();

            // Create default business hours
            $defaultHours = [
                ['day_of_week' => 'monday', 'open_time' => '10:00', 'close_time' => '18:00', 'is_open' => true, 'sort_order' => 1],
                ['day_of_week' => 'tuesday', 'open_time' => '10:00', 'close_time' => '18:00', 'is_open' => true, 'sort_order' => 2],
                ['day_of_week' => 'wednesday', 'open_time' => '10:00', 'close_time' => '18:00', 'is_open' => true, 'sort_order' => 3],
                ['day_of_week' => 'thursday', 'open_time' => '10:00', 'close_time' => '18:00', 'is_open' => true, 'sort_order' => 4],
                ['day_of_week' => 'friday', 'open_time' => '10:00', 'close_time' => '19:00', 'is_open' => true, 'sort_order' => 5],
                ['day_of_week' => 'saturday', 'open_time' => '10:00', 'close_time' => '19:00', 'is_open' => true, 'sort_order' => 6],
                ['day_of_week' => 'sunday', 'open_time' => null, 'close_time' => null, 'is_open' => false, 'sort_order' => 7],
            ];

            foreach ($defaultHours as $hour) {
                BusinessHours::create($hour);
            }

            return response()->json([
                'message' => 'Business hours reset to default successfully',
                'businessHours' => BusinessHours::orderBy('sort_order')->get()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to reset business hours: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get business hours for public display
     */
    public function getPublicHours()
    {
        $businessHours = BusinessHours::orderBy('sort_order')->get();

        return response()->json([
            'businessHours' => $businessHours->map(function ($hour) {
                return [
                    'day' => ucfirst($hour->day_of_week),
                    'hours' => $hour->getFormattedDisplayText(),
                    'is_open' => $hour->is_open,
                    'is_closed' => !$hour->is_open
                ];
            })
        ]);
    }
}
