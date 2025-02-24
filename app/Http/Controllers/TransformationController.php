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

class TransformationController extends Controller
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function index()
    {
        try {
            $transformations = Transformation::all();
            \Log::info('Fetched transformations:', ['count' => $transformations->count()]);

            $formattedTransformations = $transformations->map(function ($transformation) {
                return [
                    'id' => $transformation->id,
                    'before_image' => url($transformation->before_image),
                    'after_image' => url($transformation->after_image),
                    'description' => $transformation->description,
                    'created_at' => $transformation->created_at
                ];
            });

            return Inertia::render('Dashboard/ManageTransformations', [
                'transformations' => $formattedTransformations
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in index: ' . $e->getMessage());
            return Inertia::render('Dashboard/ManageTransformations', [
                'transformations' => []
            ]);
        }
    }

    public function store(Request $request)
    {
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
            'barber_id' => $validated['barber_id'],
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
