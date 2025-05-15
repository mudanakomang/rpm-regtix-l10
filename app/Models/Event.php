<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $fillable = [
        'name',
        'start_date',
        'end_date',
        'location',
        'location_gmaps_url',
        'code_prefix',
        'registration_start_date',
        'registration_end_date',
        'description',
        'rpc_collection_days',
        'rpc_collection_dates',
        'rpc_collection_times',
        'rpc_collection_location',
        'rpc_collection_gmaps_url',
        'event_url',
        'ig_url',
        'fb_url',
        'contact_email',
        'contact_phone',
        'event_owner',
        'event_organizer',
        'event_logo',
        'event_banner',
    ];


    public function registrations()
    {
        return $this->hasManyThrough(Registration::class, TicketType::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
