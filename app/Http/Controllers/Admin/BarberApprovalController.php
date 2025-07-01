<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarberApprovalController extends Controller
{
    /**
     * Display the barber approvals page.
     */
    public function index()
    {
        $pendingBarbers = Barber::with(['user' => function($query) {
            $query->whereNull('deleted_at');
        }])
            ->where('is_approved', false)
            ->get()
            ->filter(function($barber) {
                return $barber->user !== null;
            })
            ->values();

        return Inertia::render('Admin/BarberApprovals', [
            'pendingBarbers' => $pendingBarbers
        ]);
    }

    /**
     * Approve a barber.
     */
    public function approve(Barber $barber)
    {
        $barber->update(['is_approved' => true]);

        return back()->with('success', 'Barber has been approved successfully.');
    }

    /**
     * Decline a barber.
     */
    public function decline(Barber $barber)
    {
        $barber->update(['is_approved' => 0]);
        return back()->with('success', 'Barber has been declined.');
    }
}
