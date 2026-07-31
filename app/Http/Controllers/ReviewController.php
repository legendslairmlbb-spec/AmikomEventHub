<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000'
        ]);

        Review::create([
            'event_id' => $event->id,
            'reviewer_name' => Auth::check() ? Auth::user()->name : 'Anonim',
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Terima kasih, ulasan Anda berhasil dikirim!');
    }
}
