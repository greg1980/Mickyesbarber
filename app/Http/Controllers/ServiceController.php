<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('ServicePage');
    }
}
