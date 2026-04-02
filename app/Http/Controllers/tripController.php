<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Apps\Models\Trip;
use Inertia\Inertia;


class tripController extends Controller
{
    public function index() {
    return Inertia::render('Passenger/Index', [
        // 'student' is the relationship name in your Trip Model
        'trip' => Trip::with('studentID')
            ->where('available_seats', '>', 0)
            ->get()
    ]);
}
}
