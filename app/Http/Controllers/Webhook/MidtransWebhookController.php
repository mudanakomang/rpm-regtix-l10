<?php

namespace App\Http\Controllers\Webhook;

use App\Helpers\GenerateBib;
use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        $isProd = config('midtrans.isProduction');
        $serverKey = $isProd ? config('midtrans.serverKeyProd') : config('midtrans.serverKeySb');
        if(!$this->verifySignature($data, $serverKey)){
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $orderId = $data['order_id'];
        $originalOrderId = Str::beforeLast($orderId, '-');
        $transactionId = $data['transaction_id'];
        $transactionStatus = $data['transaction_status'];
        $transactionTime = $data['transaction_time'] ?? $data['settlement_time'] ?? null;
        $paymentType = $data['payment_type'];
        $grossAmount = $data['gross_amount'];

        // Remove expired registration
        if($transactionStatus === 'expire') {
            Registration::where('registration_code', $originalOrderId)->delete();
        }

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $this->updatePaymentStatus($originalOrderId, $transactionId, 'paid', $transactionTime, $paymentType, $grossAmount);
                break;
            case 'pending':
                $this->updatePaymentStatus($originalOrderId, $transactionId, 'pending', $transactionTime, $paymentType, $grossAmount);
                break;
            case 'cancel':
                $this->updatePaymentStatus($originalOrderId, $transactionId, 'cancel', $transactionTime, $paymentType, $grossAmount);
                break;
        }
        Log::info($data);

        return response()->json(['message' => 'Success'], 200);
    }

    private function verifySignature($data, $serverKey): bool
    {
        if (!isset($data['signature_key'], $data['order_id'], $data['status_code'], $data['gross_amount'])) {
            return false;
        }

        $expected = hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey);

        return $expected === $data['signature_key'];
    }

    private function updatePaymentStatus(string $originalOrderId, string $transactionId, string $status, string $transactionTime, string $paymentType, $grossAmount): void
    {
        $registration = Registration::where('registration_code', $originalOrderId)->first();
        $regIdGenerator = new GenerateBib();
        $regId = $regIdGenerator->generateRegId();
        if($registration){
            $registration->update([                
                'status' => 'confirmed',
                'payment_status' => $status,
                'transaction_code' => $transactionId,
                'reg_id' => $regId,
                'paid_at' => $transactionTime,
                'payment_type' => $paymentType,
                'gross_amount' => $grossAmount
            ]);

            if ($registration->voucherCode) {
                $registration->voucherCode->update([
                    'used' => true
                ]);
            }
        }
    }
}
