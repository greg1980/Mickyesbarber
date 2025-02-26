<?php

namespace App\Http\Controllers;

use App\Models\Transformation;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class TransformationController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());

        // Add auth middleware first
        $this->middleware('auth');

        // Add customer-only middleware
        $this->middleware(function ($request, $next) {
            if (!$request->user()->is_admin && $request->user()->isBarber()) {
                return redirect()->route('dashboard')->with('error', 'Only customers can access transformations.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        try {
            // Get transformations based on user role
            $transformationsQuery = Transformation::with(['barber', 'booking', 'user']);

            // If user is admin, show all transformations, otherwise show only their own
            if (!auth()->user()->is_admin) {
                $transformationsQuery->where('user_id', auth()->id());
            }

            $transformations = $transformationsQuery->get();

            // Get only completed bookings for the transformation form
            $bookings = auth()->user()->bookings()
                ->where('status', 'completed')
                ->with('barber')
                ->get();

            $barbers = \App\Models\Barber::all();

            return Inertia::render('Dashboard/ManageTransformations', [
                'transformations' => $transformations->map(function ($transformation) {
                    return [
                        'id' => $transformation->id,
                        'before_image' => url('storage/' . $transformation->before_photo),
                        'after_image' => url('storage/' . $transformation->after_photo),
                        'review' => $transformation->review,
                        'rating' => $transformation->rating,
                        'haircut_style' => $transformation->haircut_style,
                        'barber' => $transformation->barber,
                        'booking' => $transformation->booking,
                        'user' => $transformation->user,
                        'created_at' => $transformation->created_at
                    ];
                }),
                'bookings' => $bookings,
                'barbers' => $barbers,
                'is_admin' => auth()->user()->is_admin
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in index: ' . $e->getMessage());
            return Inertia::render('Dashboard/ManageTransformations', [
                'transformations' => [],
                'bookings' => [],
                'barbers' => [],
                'is_admin' => auth()->user()->is_admin
            ]);
        }
    }

    public function store(Request $request)
    {
        // Ensure the user owns the booking they're reviewing
        $booking = Booking::findOrFail($request->booking_id);
        if ($booking->user_id !== auth()->id()) {
            return back()->with('error', 'You can only review your own bookings.');
        }

        // Ensure the booking is completed
        if ($booking->status !== 'completed') {
            return back()->with('error', 'You can only review completed bookings.');
        }

        $validated = $request->validate([
            'before_photo' => 'required|image',
            'after_photo' => 'required|image',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
            'haircut_style' => 'required|string',
            'booking_id' => 'required|exists:bookings,id',
            'barber_id' => 'required|exists:barbers,id'
        ]);

        // Store the photos
        $beforePath = $request->file('before_photo')->store('transformations', 'public');
        $afterPath = $request->file('after_photo')->store('transformations', 'public');

        // Create the transformation
        $transformation = Transformation::create([
            'user_id' => auth()->id(),
            'barber_id' => $booking->barber_id, // Use the barber from the booking
            'booking_id' => $validated['booking_id'],
            'before_photo' => $beforePath,
            'after_photo' => $afterPath,
            'rating' => $validated['rating'],
            'review' => $validated['review'],
            'haircut_style' => $validated['haircut_style']
        ]);

        return back()->with('success', 'Thank you for your review!');
    }

    public function destroy(Transformation $transformation)
    {
        // Ensure the user owns this transformation
        if ($transformation->user_id !== auth()->id()) {
            return back()->with('error', 'You can only delete your own transformations.');
        }

        try {
            // Delete the image files
            Storage::disk('public')->delete([
                str_replace('storage/', '', $transformation->before_image),
                str_replace('storage/', '', $transformation->after_image)
            ]);

            // Delete the record
            $transformation->delete();

            return redirect()->back()->with('success', 'Transformation deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting transformation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete transformation');
        }
    }

    public function getBarberTransformations($barberId)
    {
        $transformations = Transformation::with('user')
            ->where('barber_id', $barberId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($transformations);
    }
}
