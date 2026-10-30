<?php

use App\Http\Controllers\ContatoController;
use App\Http\Controllers\ImController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendarController;

Route::get('/', function () {
    return view('index');
});

Route::get('/imc', [ImController::class, 'index'])->name('imc.index');
Route::post('/imc/calculate', [ImController::class, 'calculate'])->name('imc.calculate');

Route::get('/about', function () {
    return view('about');
}); 

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('/terms', function () {
    return view('terms');
});

Route::get('/faq', function () {
    return view('faq');
});

Route::get('/agendamento', [AgendarController::class, 'index']);

Route::post('/contact', [ContatoController::class, 'enviar'])->name('contato.enviar');
