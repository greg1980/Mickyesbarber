<?php

namespace App\Http\Controllers;

use App\Models\Transformation;
use App\Models\Booking;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TransformationController extends Controller
{
    // List transformations
    public function index()
    {
        $user = Auth::user();
        $query = Transformation::with(['barber', 'user']);

        if ($user->role === 'admin') {
            $transformations = $query->get();
        } else {
            $transformations = $query->where('user_id', $user->id)
                ->orWhere(function ($q) {
                    $q->where('is_approved', true);
                })
                ->get();
        }

        // Provide all barbers for the dropdown
        $barbers = Barber::with('user')->get();

        // Find the latest completed booking for the logged-in customer
        $latestBooking = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time')
            ->first();

        $alreadySubmitted = false;
        if ($latestBooking) {
            $alreadySubmitted = Transformation::where('user_id', $user->id)
                ->where('booking_id', $latestBooking->id)
                ->exists();
        }

        return Inertia::render('Transformations/Index', [
            'transformations' => $transformations,
            'is_admin' => $user->role === 'admin',
            'barbers' => $barbers,
            'latestBookingId' => $latestBooking ? $latestBooking->id : null,
            'alreadySubmitted' => $alreadySubmitted,
        ]);
    }

    // Store a new transformation (pending approval)
    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'before_photo' => 'required|image',
            'after_photo' => 'required|image',
            'style' => 'required|string',
            'review' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
            'barber_id' => 'required|exists:barbers,id',
            'booking_id' => 'required|exists:bookings,id',
        ]);

        // Store images
        $beforePath = $request->file('before_photo')->store('transformations', 'public');
        $afterPath = $request->file('after_photo')->store('transformations', 'public');

        \Log::info('Raw request style:', ['style' => $request->input('style')]);
        \Log::info('Validated data:', $validated);

        $transformation = Transformation::create([
            'user_id' => $user->id,
            'barber_id' => $validated['barber_id'],
            'booking_id' => $validated['booking_id'],
            'style' => $validated['style'],
            'review' => $validated['review'],
            'rating' => $validated['rating'],
            'before_photo' => $beforePath,
            'after_photo' => $afterPath,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Transformation submitted and pending admin approval.');
    }

    // Admin approves a transformation
    public function approve($id)
    {
        $this->authorize('admin');
        $transformation = Transformation::findOrFail($id);
        $transformation->is_approved = true;
        $transformation->save();
        return back()->with('success', 'Transformation approved!');
    }

    // Delete a transformation
    public function destroy($id)
    {
        $transformation = Transformation::findOrFail($id);
        $user = Auth::user();
        if ($user->id !== $transformation->user_id && $user->role !== 'admin') {
            abort(403);
        }
        // Delete images
        Storage::disk('public')->delete([$transformation->before_photo, $transformation->after_photo]);
        $transformation->delete();
        return back()->with('success', 'Transformation deleted.');
    }

    // Public method to get approved transformations for homepage
    public function getApprovedTransformations()
    {
        $transformations = Transformation::where('is_approved', true)
            ->with(['user', 'barber'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($transformation) {
                return [
                    'id' => $transformation->id,
                    'before' => $transformation->before_photo ? Storage::url($transformation->before_photo) : null,
                    'after' => $transformation->after_photo ? Storage::url($transformation->after_photo) : null,
                    'style' => $transformation->style,
                    'created_at' => $transformation->created_at,
                ];
            });

        return response()->json($transformations);
    }
}
