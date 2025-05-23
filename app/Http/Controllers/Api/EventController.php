<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $events = Event::with(['categories', 'categories.ticketTypes', 'categories.categoryTicketTypes'])->paginate($perPage);

        return EventResource::collection($events);
    }

    public function show($id) {
        if (is_numeric($id)) {
            $event = Event::with(['categories', 'categories.ticketTypes'])->findOrFail($id);
        } else {
            $event = Event::with(['categories', 'categories.ticketTypes'])->where('slug', $id)->firstOrFail();
        }

        return new EventResource($event);
    }
}
