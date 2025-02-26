<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use App\Mail\BarberRegistration;
use Illuminate\Support\Facades\Mail;

class BarberController extends Controller
{
    public function showRegistrationForm()
    {
        return Inertia::render('Barber/Register');
    }

    public function register(Request $request)
    {
        try {
            \Log::info('Starting barber registration process');

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'bio' => 'required|string',
                'years_of_experience' => 'required|integer|min:0',
                'specialties' => 'required|array|min:1',
                'profile_photo' => 'required|image|max:2048', // max 2MB
            ]);

            \Log::info('Validation passed', $validated);

            // Store the profile photo
            $profilePhotoPath = $request->file('profile_photo')->store('barber-photos', 'public');
            \Log::info('Profile photo stored at: ' . $profilePhotoPath);

            // Check if user is already a barber
            if (auth()->user()->isBarber()) {
                \Log::warning('User is already a barber', ['user_id' => auth()->id()]);
                return back()->with('error', 'You are already registered as a barber.');
            }

            // Create the barber profile with user_id
            $barberData = [
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'bio' => $validated['bio'],
                'years_of_experience' => $validated['years_of_experience'],
                'specialties' => $validated['specialties'],
                'profile_photo' => $profilePhotoPath,
                'is_available' => true,
            ];

            \Log::info('Attempting to create barber with data:', $barberData);

            $barber = Barber::create($barberData);
            \Log::info('Barber created successfully', ['barber_id' => $barber->id]);

            // Send registration confirmation email
            Mail::to(auth()->user()->email)->send(new BarberRegistration($barber));

            return redirect()->route('dashboard')->with([
                'success' => 'Successfully registered as a barber!',
                'message' => 'Your barber profile has been created successfully.',
                'type' => 'success'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error during barber registration', ['errors' => $e->errors()]);
            // If there's a validation error, delete any uploaded photo
            if (isset($profilePhotoPath)) {
                Storage::disk('public')->delete($profilePhotoPath);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error during barber registration: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // If there's any other error, delete any uploaded photo
            if (isset($profilePhotoPath)) {
                Storage::disk('public')->delete($profilePhotoPath);
            }

            return back()->with([
                'error' => 'Failed to register as a barber.',
                'message' => 'An error occurred while creating your barber profile. Please try again.',
                'type' => 'error'
            ]);
        }
    }

    public function toggleAvailability(Request $request, Barber $barber)
    {
        // Ensure the authenticated user has permission to modify this barber's availability
        if (!Auth::user()->isBarber() || Auth::user()->barber->id !== $barber->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $barber->update([
            'is_available' => !$barber->is_available
        ]);

        return response()->json([
            'message' => $barber->is_available ? 'You are now available for bookings' : 'You are now unavailable for bookings',
            'is_available' => $barber->is_available
        ]);
    }

    public function getAvailability(Barber $barber)
    {
        return response()->json([
            'is_available' => $barber->is_available
        ]);
    }
}
