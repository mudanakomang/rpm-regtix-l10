<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    //
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'gender',
        'place_of_birth',
        'dob',
        'address',
        'district',
        'province',
        'country',
        'id_card_type',
        'id_card_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_type',
        'nationality',
        'jersey_size',
        'community_name',
        'bib_name',
        'reg_id',
        'is_validated',
        'validated_by',
        'registration_date'
    ];

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
    public function event()
    {
        return $this->hasOneThrough(Event::class, TicketType::class, 'id', 'id', 'ticket_type_id', 'event_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
