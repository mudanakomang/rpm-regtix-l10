<?php

namespace App\Helpers;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\ApiException;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Model\SendSmtpEmailTo;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class EmailSender 
{
    // send email
    public function sendEmail($registration, $subject, $template) {
        try {
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));

            $apiInstance = new TransactionalEmailsApi(
                new Client(),
                $config
            );
            $event = $registration->categoryTicketType->category->event;
            $category = $registration->categoryTicketType->category;
            $ticketType = $registration->categoryTicketType->ticketType;
            $voucher = $registration->voucherCode->voucher ?? null;
            $categoryTicketType = $registration->categoryTicketType;
            $price = $categoryTicketType->price;
            $priceReduction = $voucher ? $categoryTicketType->price * ($voucher->discount / 100) : 0;
            $finalPrice = $categoryTicketType->price - $priceReduction;

            $voucherCode = $registration->voucherCode;

            $params = [
                'name' => $registration->full_name,
                'phone' => $registration->phone,
                'email' => $registration->email,
                'event' => $event->name,
                'location' => $event->location,
                'date' => Carbon::parse($event->start_date)->format('d M Y H:i'),
                'rpc_location' => $event->rpc_collection_location,
                'rpc_date' => $event->rpc_collection_days . ' ' . $event->rpc_collection_dates . ' ' . $event->rpc_collection_times,
                'category' => $category->name,
                'qr_code_path' => $registration->qr_code_path,
                'bib' => $registration->bib_name,
                'bib_number' => $registration->reg_id,
                'ticket' => $ticketType->name,
                'registration_code' => $registration->registration_code,
                'payment_url' => $registration->payment_url,
                'date' => Carbon::parse($registration->created_at)->format('d M Y'),
                'cek_registrasi' => 'https://regtix.id/registrations/' . $registration->registration_code,
                'item' => 'Tiket '. $event->name . ' - ' . $category->name . ' - ' . $ticketType->name,
                'price' => $this->formatMoney($price),
                'price_reduction' => '- '.$this->formatMoney($priceReduction),
                'final_price' => $this->formatMoney($finalPrice),
                'voucher' => $voucher ? $voucherCode->code : 'No Voucher',
                'year' => Carbon::now()->year
            ];
            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => $subject,
                'sender' => ['name' => 'RegTix | ' . $event->name, 'email' => env('MAIL_SENDER')],
                'replyTo' => ['name' => 'RegTix | ' . $event->name, 'email' => env('MAIL_REPLY_TO')],
                'to' => [new SendSmtpEmailTo(['email' => $registration->email])], 
                'htmlContent' => $template,
                'params' => $params
            ]);

            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            Log::info($result);
        } catch (ApiException $e) {
            Log::error($e->getMessage());
        }
    }

    function formatMoney($angka){
        $hasil_rupiah = "Rp " . number_format($angka,2,',','.');
        return $hasil_rupiah;
    }
}