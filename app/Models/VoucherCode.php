<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'code',
        'used',
        'registration_id'
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
