<?php

use App\Filament\Resources\RegistrationResource\Pages\ValidateRegistration;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/admin/registrations/{registration}/validate', ValidateRegistration::class)->name('registrations.validate');
