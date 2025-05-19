<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class MidtransUtils
{
    public static function generatePaymentLink($registration, $event)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $base64Auth = base64_encode($serverKey . ':');
        $auth = 'Basic ' . $base64Auth;

        $voucher = $registration->voucherCode->voucher ?? null;
        $categoryTicketType = $registration->categoryTicketType;
        $priceReduction = $voucher ? $categoryTicketType->price * ($voucher->discount / 100) : 0;
        $finalPrice = $categoryTicketType->price - $priceReduction;
        $address = $registration->address . ', ' . $registration->district . ', ' . $registration->province;
        dd($address);

        $itemDetails = [
            [
                'id' => 'item-'.$registration->id,
                'price' => intval($registration->categoryTicketType->price),
                'quantity' => 1,
                'name' => $event->name . '-' .  $registration->categoryTicketType->category->name . ' - ' . $registration->categoryTicketType->ticketType->name . 'Ticket',
            ]
        ];

        if($voucher) {
            $itemDetails[] = [
                'id' => 'voucher-'.$registration->id,
                'price' => -$priceReduction,
                'quantity' => 1,
                'name' => $voucher = $registration->voucherCode->voucher->name,
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $registration->registration_code,
                'gross_amount' => $finalPrice,
                'payment_link_id' => "payment-for-" .$registration->registration_code
            ],
            "usage_limit" => 1,
            'customer_details' => [
                'first_name' => $registration->full_name,                
                'email' => $registration->email,
                'phone' => $registration->phone,
                'billing_address' => [
                    'address' => $address,
                    'country_code' => 'IDN'
                ],
                'shipping_address' => [
                    'address' => $address,
                    'country_code' => 'IDN'
                ],
                'customer_details_required_fields' => [
                    'first_name',
                    'email',
                    'phone',
                ],
            ],
            'expiry' => [
                'duration' => 1,
                'unit' => 'days'
            ],
            'item_details' => $itemDetails,
            'title' => $event->name ." ticket payment",
            'callbacks' => [
                'finish' => 'https://regtix.id/payment/finish/' . $registration->registration_code,               
            ]
        ];

        $client = new Client();
        $response = $client->request('POST', env('MIDTRANS_API_URL'), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => $auth
            ],
            'body' => json_encode($payload)
        ]);

        return json_decode($response->getBody()->getContents(), true);
        
    }
}