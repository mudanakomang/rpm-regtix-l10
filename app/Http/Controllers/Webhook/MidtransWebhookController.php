<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $transactionId = $data['transaction_id'];
        $transactionStatus = $data['transaction_status'];
        
        // Remove expired registration
        if($transactionStatus === 'expire')
        {
            Registration::where('registration_code', $orderId)->delete();
        }

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $this->updatePaymentStatus($orderId, $transactionId, 'paid');
                break;
            case 'pending':
                $this->updatePaymentStatus($orderId, $transactionId, 'pending');
                break;
            case 'cancel':
                $this->updatePaymentStatus($orderId, $transactionId, 'cancel');
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

    private function updatePaymentStatus(string $orderId, string $transactionId, string $status): void
    {
        $registration = Registration::where('registration_code', $orderId)->first();
        
        if($registration){
            $registration->update([                
                'status' => 'confirmed',
                'payment_status' => $status,
                'transaction_code' => $transactionId,
            ]);
        }
    }
}
