<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VoucherCodeCheckResource;
use App\Models\VoucherCode;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function checkVoucherCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $voucherCode = VoucherCode::with(['voucher.categoryTicketType.ticketType', 'registration'])
            ->where('code', $request->code)
            ->first();

        if (!$voucherCode) {
            return response()->json(['message' => 'Kode voucher tidak ditemukan.'], 404);
        }

        return new VoucherCodeCheckResource($voucherCode);
    }
}
