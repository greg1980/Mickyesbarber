<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Barber;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'total_barbers' => Barber::count(),
                'active_barbers' => Barber::where('is_available', true)->count(),
            ]
        ]);
    }

    public function users()
    {
        return Inertia::render('Admin/Users', [
            'users' => User::with('barber')
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        ]);
    }

    public function toggleUserStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);

        return back()->with('success', 'User status updated successfully');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }

    public function assignBarber(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'required|string',
            'years_of_experience' => 'required|integer|min:0',
            'specialties' => 'required|array|min:1',
            'profile_photo' => 'required|image|max:2048',
        ]);

        $profilePhotoPath = $request->file('profile_photo')->store('barber-photos', 'public');

        Barber::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'bio' => $validated['bio'],
            'years_of_experience' => $validated['years_of_experience'],
            'specialties' => $validated['specialties'],
            'profile_photo' => $profilePhotoPath,
            'is_available' => true,
        ]);

        return back()->with('success', 'User assigned as barber successfully');
    }

    public function removeBarber(User $user)
    {
        $user->barber()->delete();
        return back()->with('success', 'Barber role removed successfully');
    }
}
