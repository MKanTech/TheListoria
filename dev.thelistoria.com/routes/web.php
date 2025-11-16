<?php

use Illuminate\Support\Facades\Route;
// ... diğer mevcut kodlar ...

// Sizin değiştirdiğiniz ana sayfa rotası
Route::get('/', function () {
    return view('welcome'); // welcome.blade.php dosyasını gösterir
})->name('home');


// Kullanıcı Giriş/Kayıt Rotaları için Controller tanımları
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Kullanıcı Kayıt Sayfası ve İşlemi
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Kullanıcı Giriş/Çıkış Sayfası ve İşlemi
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');