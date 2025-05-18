<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected static function booted()
    {
        static::created(function ($event) {
            $oldLogoPath = $event->event_logo;
            $newLogoPath = "image/event/logo/{$event->id}/" . basename($oldLogoPath);

            $oldBannerPath = $event->event_banner;
            $newBannerPath = "image/event/banner/{$event->id}/" . basename($oldBannerPath);

            if (Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->move($oldLogoPath, $newLogoPath);
                $event->updateQuietly(['event_logo' => $newLogoPath]);
            }

            if (Storage::disk('public')->exists($oldBannerPath)) {
                Storage::disk('public')->move($oldBannerPath, $newBannerPath);
                $event->updateQuietly(['event_banner' => $newBannerPath]);
            }
        });
    }
}
