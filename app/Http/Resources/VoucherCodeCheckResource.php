<?php

namespace App\Http\Resources;

use App\Models\CategoryTicketType;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherCodeCheckResource extends JsonResource
{
    public function toArray($request)
    {
        $voucher = $this->voucher;
        $ticketType = $voucher?->categoryTicketType->ticketType;
        $registration = $this->registration;
        $categoryTicketType = $voucher?->categoryTicketType;
        $isUsed = (bool) $this->used;
        $priceReduction = $categoryTicketType->price * ($voucher->discount / 100);
        $finalPrice = $categoryTicketType->price - $priceReduction;

        $categoryTicketTypeWithCount = CategoryTicketType::withCount('registrations')->find($categoryTicketType->id);
        $used = $categoryTicketTypeWithCount?->registrations_count ?? 0;
        return [
            'code' => $this->code,
            'is_used' => $isUsed,
            'is_valid' => !$isUsed, // valid jika belum digunakan
            'voucher' => [
                'id' => $voucher?->id,
                'name' => $voucher?->name,
                'discount' => $voucher?->discount,
            ],

            'ticket_type' => $ticketType ? [
                'id' => $ticketType->id,
                'name' => $ticketType->name,
                'price' => $categoryTicketType->price,
                'quota' => $categoryTicketType->quota,
                'used' => $used,
                'remaining' => $categoryTicketType->quota - $used,
                'valid_from' => $categoryTicketType->valid_from,
                'valid_until' => $categoryTicketType->valid_until,
                'final_price' => $finalPrice,
            ] : null,
            'registration' => $registration ? [
                'id' => $registration->id,
                'full_name' => $registration->full_name,
                'email' => $registration->email,
            ] : null,
        ];
    }
}
