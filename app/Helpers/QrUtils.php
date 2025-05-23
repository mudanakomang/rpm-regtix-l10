<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrUtils {
    public function generateQr($registration)
    {
        $fileName = 'qrcodes/'.$registration->id. '/'.$registration->registration_code.'.png';
        $qr = QrCode::format('png')->size(200)->generate($registration->registration_code);

        Storage::disk('public')->put($fileName, $qr);

        return Storage::url($fileName);
    }
}