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
        'code_prefix',
    ];
    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function registrations()
    {
        return $this->hasManyThrough(Registration::class, TicketType::class);
    }
}
