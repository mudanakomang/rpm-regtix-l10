<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    //
    protected $fillable = [
        'name',
        'price',
        'quota',
        'event_id',
    ];
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
