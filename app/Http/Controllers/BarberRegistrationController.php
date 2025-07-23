<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Barber;
use Illuminate\Support\Facades\Validator;

class BarberRegistrationController extends Controller
{
    // Show the registration form
    public function create()
    {
        // Only allow logged-in users
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to register as a barber.');
        }
        return inertia('Barber/Register'); // Vue page to be created
    }

    // Handle form submission
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to register as a barber.');
        }

        $validator = Validator::make($request->all(), [
            'bio' => 'required|string|max:1000',
            'years_of_experience' => 'required|integer|min:0|max:80',
            'mobile_contact' => [
                'required',
                'string',
                'max:30',
                'regex:/^[0-9\-\+\(\)\s]+$/'
            ],
        ]);

        $errors = $validator->errors();

        // Prevent reapplication if declined
        if (Barber::where('user_id', Auth::id())->where('is_approved', 0)->exists()) {
            $errors->add('mobile_contact', 'Your previous barber application was declined. You cannot reapply.');
        }

        if (Barber::where('user_id', Auth::id())->exists()) {
            // Get any existing errors for mobile_contact
            $existing = $errors->get('mobile_contact');
            // Add the duplicate registration error
            $errors->add('mobile_contact', 'You have already applied or are already a barber.');
            // Re-add any previous errors to ensure both are present
            foreach ($existing as $err) {
                $errors->add('mobile_contact', $err);
            }
        }

        if ($errors->any()) {
            return back()->withErrors($errors)->withInput();
        }

        $validated = $validator->validated();

        $barber = Barber::create([
            'user_id' => Auth::id(),
            'bio' => $validated['bio'],
            'years_of_experience' => $validated['years_of_experience'],
            'mobile_contact' => $validated['mobile_contact'],
            'is_approved' => null,
        ]);

        // Notify admin(s) of new barber application
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'system',
                'title' => 'New Barber Application',
                'message' => Auth::user()->name . ' has applied to become a barber.',
                'data' => ['user_id' => Auth::id(), 'barber_id' => $barber->id],
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Your application has been submitted and is pending admin approval.');
    }
}
