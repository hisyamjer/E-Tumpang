<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class historyController extends Controller
{
    public function index()
    {
        return Inertia::render('History/index');
    }
}

