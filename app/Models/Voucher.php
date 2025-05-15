<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_ticket_type_id',
        'discount',
    ];

    public function categoryTicketType()
    {
        return $this->belongsTo(CategoryTicketType::class);
    }

    public function voucherCodes()
    {
        return $this->hasMany(VoucherCode::class);
    }
}
