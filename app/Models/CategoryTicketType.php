<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryTicketType extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'ticket_type_id',
        'price',
        'quota',
        'valid_from'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

}
