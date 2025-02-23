<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Transformation;
use App\Models\Service;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('HomePage', [
            'transformations' => Transformation::latest()->get(),
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }
}
