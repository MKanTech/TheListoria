<?php

// TÜM CONTROLLER TANIMLARI EN ÜSTTE OLMALIDIR
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PListController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ana Sayfa Rotası
Route::get('/', function () {
    return view('welcome');
})->name('home');

// --- 1. Kullanıcı Giriş/Kayıt Rotaları ---
// Bu rotalar genellikle 'guest' middleware altında gruplanır, 
// ancak mevcut hali de çalışır.
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');


// --- 2. Listeler Rotaları (Sadece Giriş Yapanlar İçin) ---
Route::middleware(['auth'])->group(function () {
    // Listeleri göster: /lists
    Route::get('/lists', [PListController::class, 'index'])->name('lists.index');

    // Yeni liste oluşturma formu: /lists/create
    Route::get('/lists/create', [PListController::class, 'create'])->name('lists.create');

    // Yeni listeyi kaydetme işlemi
    Route::post('/lists', [PListController::class, 'store'])->name('lists.store');

    // Liste detay sayfasını göster (Listenin ID'si ile)
    Route::get('/lists/{list}', [PListController::class, 'show'])->name('lists.show');

    // Listenin içine yeni öğe ekleme işlemi
    Route::post('/lists/{list}/items', [PListController::class, 'storeItem'])->name('lists.items.store');
    // Listeler Rotaları (Sadece Giriş Yapanlar İçin)
Route::middleware(['auth'])->group(function () {
    // ... mevcut rotalar ...

    // Listenin içine yeni öğe ekleme işlemi
    Route::post('/lists/{list}/items', [PListController::class, 'storeItem'])->name('lists.items.store');

    // Öğenin tamamlama durumunu değiştirme işlemi (YENİ)
    Route::post('/lists/{list}/items/{item}/toggle', [PListController::class, 'toggleItem'])->name('lists.items.toggle');
});
});