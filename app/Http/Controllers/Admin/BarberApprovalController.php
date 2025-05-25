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
        $pendingBarbers = Barber::with('user')
            ->where('is_approved', false)
            ->get();

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
}
