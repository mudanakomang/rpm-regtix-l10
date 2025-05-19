<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MidtransUtils;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegistrationRequest;
use App\Http\Resources\RegistrationResource;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Voucher;
use App\Models\VoucherCode;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegistrationController extends Controller
{
    public function store(StoreRegistrationRequest $request)
    {

        try {
            $data = $request->validated();
            $event = Event::find($data['event_id']);

            if (!$event) {
                return response()->json(['message' => 'Event not found.'], 404);
            }
            $prefix = $event->code_prefix;
            // Generate registration ID
            $data['registration_code'] = 'RTIX-' . $prefix . '-' . strtoupper(Str::random(6));
            $data['registration_date'] = Carbon::now();
            $data['status'] = 'pending';
            $data['payment_status'] = 'pending';
            $data['reg_id'] = "";



            $registration = Registration::create($data);

            $code = $data['voucher_code'] ?? null;
            if ($code) {
                VoucherCode::where('code', $code)
                    ->whereDoesntHave('registration')
                    ->where('used', false)
                    ->update(['registration_id' => $registration->id]);
            }


            $ticketType = $registration->categoryTicketType->ticketType;
            $categoryTicketType = $registration->categoryTicketType;
            $regData = $registration->toArray();
            $regData['event'] = $event->toArray();
            $voucher = $registration->voucherCode->voucher ?? null;
            $priceReduction = $voucher ? $categoryTicketType->price * ($voucher->discount / 100) : 0;

            $regData = $registration->makeHidden('category_ticket_type')->toArray();

            $regData['category'] = $registration->categoryTicketType->category->toArray();
            $regData['voucher_code'] = $registration->voucherCode ? [
                ...$registration->voucherCode->toArray(),
                'voucher' => $voucher ? [
                    'id' => $voucher->id,
                    'name' => $voucher->name,
                    'discount' => $voucher->discount,
                ] : null,
            ] : null;
            $finalPrice = $categoryTicketType->price - $priceReduction;
            $regData['ticket_type'] = [
                'name' => $ticketType->name,
                'price' => $categoryTicketType->price,
                'quota' => $categoryTicketType->quota,
                'used' => $categoryTicketType->registrations_count,
                'remaining' => $categoryTicketType->quota - $categoryTicketType->registrations_count,
                'valid_from' => $categoryTicketType->valid_from,
                'valid_until' => $categoryTicketType->valid_until,
                'final_price' => $finalPrice,
            ];

            $shouldPay = $finalPrice > 0;
            if ($shouldPay) {
                $midtrans = new MidtransUtils();
                $paymment = $midtrans->generatePaymentLink($registration, $event);

                $regData['payment_url'] = $paymment['payment_url'];

                $registration->update(['payment_url' => $paymment['payment_url']]);
            }
            return response()->json([
                'message' => 'Registration successful.',
                'data' => $regData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
