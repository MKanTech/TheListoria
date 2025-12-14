<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserListController;

Route::get('/', function () {
    return view('welcome');
});

// Misafir Kullanıcı Rotları (Henüz giriş yapmamış)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Giriş Yapmış Kullanıcı Rotları
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Liste Yönetimi Rotası
    Route::get('/lists/create', [UserListController::class, 'create'])->name('lists.create');
    Route::post('/lists', [UserListController::class, 'store'])->name('lists.store');
    Route::get('/lists/{list}/edit', [UserListController::class, 'edit'])->name('lists.edit');
    Route::put('/lists/{list}', [UserListController::class, 'update'])->name('lists.update');
    Route::delete('/lists/{list}', [UserListController::class, 'destroy'])->name('lists.destroy');
    // Liste Detay Rotası (Giriş yapılmış olmalı)
    Route::get('/lists/{list}', [UserListController::class, 'show'])->name('lists.show');
    // Listeden İçerik Çıkarma Rotası
    // Post ID'sini parametre olarak alıyoruz.
    Route::delete('/lists/{list}/remove/{postId}', [UserListController::class, 'removePost'])->name('lists.removePost');
    // İçerik Yönetimi Rotası (Resource'a Benzer)
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    // Düzenleme ve Güncelleme Rotları
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    // YENİ EKLENECEK ROTA (Detay Görüntüleme)
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    // SİLME ROTASI
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    
});

// Anasayfa (Giriş yapılsa da yapılmasa da görünür)
Route::get('/', function () {
    return view('home.index'); // home.index görünümü varsayılacak
})->name('home');

// Kullanıcı Profili Rotası
Route::get('/profile/{username}', [UserController::class, 'showProfile'])->name('profile.show');