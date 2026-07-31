<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;

class EventController extends Controller
{
    public function show(Event $event)
    {
        $categories = Category::all();
        $event->load('reviews', 'category');
        return view('event-detail', compact('categories', 'event'));
    }
}
