<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class BarberRegisterController extends Controller
{
    /**
     * Show the barber registration form.
     */
    public function showForm()
    {
        return Inertia::render('Auth/BarberRegister');
    }

    /**
     * Handle the barber registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_BARBER,
        ]);

        Barber::create([
            'user_id' => $user->id,
            'bio' => $request->bio,
            'is_approved' => false,
        ]);

        // Notify admin(s) of new barber application
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'type' => 'system',
                'title' => 'New Barber Application',
                'message' => $user->name . ' has applied to become a barber.',
                'data' => ['user_id' => $user->id],
            ]);
        }

        return redirect()->route('login')
            ->with('success', 'Your barber account has been created and is pending approval. We will notify you once approved.');
    }
}
