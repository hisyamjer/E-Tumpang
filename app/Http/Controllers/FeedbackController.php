<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Feedback;
use App\Models\Trip;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'tripID'=>'required|exists:trips,tripID',
            'rating'=>'required|integer|min:1|max:5',
            'comment'=>'nullable|string|max:500',
        ]);

        $trip = Trip::where('tripID', $validated['tripID'])->firstOrFail();

        $hasBooking = $trip->bookings()
            ->where('studentID',$user->studentID)
            ->exists();

        if(!$hasBooking){
            return back()->with('error','You are not the passenger of this trip!');
        }

        $alreadyRate = Feedback::where('tripID',$validated['tripID'])
            ->where('studentID',$user->studentID)
            ->exists();

        if($alreadyRate){
            return back()->with('error','You have already rated this trip!');
        }
        
        Feedback::create([
            'tripID' => $validated['tripID'],    // Was $user->id (Wrong)
            'studentID' => $user->studentID,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'feedback_date' => now(),

        ]);

        return back()->with('message', 'Feedback submitted! Thanks for helping our community.');
    }
}
