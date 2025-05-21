<?php

namespace App\Helpers;

use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class GenerateBib {

    public function generateRegId(): string
    {
        $last = Registration::max(DB::raw('CAST(reg_id as UNSIGNED)')) ?? 0;

        $next = $last + 1;

        return str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}