<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    const GENDER = [
        'Male' => 'Male',
        'Female' => 'Female'
    ];

    const ID_CARD_TYPE = [
        'KTP' => 'KTP',
        'SIM' => 'SIM',
        'Kartu Pelajar' => 'Kartu Pelajar',
        'Passport' => 'Passport',
        'KITAS' => 'KITAS',
        'KITAP' => 'KITAP',
        'Other' => 'Other'
    ];

    const JERSEY_SIZES = [
        'S' => 'S',
        'M' => 'M',
        'L' => 'L',
        'XL' => 'XL',
        'XXL' => 'XXL',
    ];

    const BLOOD_TYPE = [
        'A' => 'A',
        'B' => 'B',
        'AB' => 'AB',
        'O' => 'O',
    ];
    //
    protected $fillable = [
        'ticket_type_id',
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

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_registration')->withPivot('status')->withTimestamps();
    }
}
