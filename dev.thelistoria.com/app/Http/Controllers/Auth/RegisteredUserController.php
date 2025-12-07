<?php

namespace App\Http\Controllers\Auth;

use App\Models\PList;
use App\Models\ListItem;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:30', 'unique:users', 'regex:/^[a-zA-Z0-9\-\._]+$/'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        $fixedLists = [
        ['title' => 'Beğendiklerim', 'description' => 'Tüm beğendiğim içerikler.', 'list_type' => 'Genel', 'is_public' => false, 'is_fixed' => true],
        ['title' => 'Tamamlananlar', 'description' => 'Bitirdiğim, izlediğim veya okuduğum içerikler.', 'list_type' => 'Genel', 'is_public' => false, 'is_fixed' => true],
        ['title' => 'Beklemede Olanlar', 'description' => 'Daha sonra izlenecek, okunacak veya yapılacak içerikler.', 'list_type' => 'Genel', 'is_public' => false, 'is_fixed' => true],
        ];

        foreach ($fixedLists as $listData) {
        $user->lists()->create($listData);
        }

        Auth::login($user);

        // Başarılı giriş sonrası ana sayfaya yönlendir
        return redirect(route('home', [], false));
    }
}