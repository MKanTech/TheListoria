<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserList; // Yeni UserList modelinizi kullanmak için ekleyin
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Doğrulama için Validator sınıfını ekleyelim

class AuthController extends Controller
{
    // Giriş sayfasını gösterir
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Kayıt sayfasını gösterir
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Kullanıcı Girişi
    public function login(Request $request)
    {
        // Kullanıcı adı veya e-posta ile giriş desteği için:
        $loginCredential = $request->input('login_credential');
        $password = $request->input('password');

        $fieldType = filter_var($loginCredential, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $fieldType => $loginCredential,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/');
        }
        
        return back()->withErrors([
            'login_credential' => 'Girilen bilgiler sistemde bulunamadı.',
        ])->onlyInput('login_credential');
    }
    
    // Yeni Kullanıcı Kaydı VE 4 Sabit Liste Oluşturma
    public function register(Request $request)
    {
        // 1. Doğrulama Kuralları
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // 2. Kullanıcıyı Oluşturma
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Diğer profil bilgileri burada eklenebilir (bio, profile_image_url vb.)
        ]);

        // 3. Zorunlu Sabit Listeleri Oluşturma
        $this->createFixedLists($user);

        // 4. Giriş yap ve yönlendir
        Auth::login($user);
        return redirect('/'); 
    }

    /**
     * Yeni kaydolan kullanıcı için 4 sabit listeyi oluşturur.
     */
    private function createFixedLists(User $user)
    {
        $fixed_lists_data = [
            // Durum (user_status) otomasyonu için temel listeler
            ['name' => 'Devam Edenler', 'type' => 'Genel', 'description' => 'Devam eden içerikleriniz!'],
            ['name' => 'Tamamlananlar', 'type' => 'Genel', 'description' => 'Tamamlanan içerikleriniz!'],
            
            // Kullanıcının manuel etkileşimi için temel listeler
            ['name' => 'Favorilerim', 'type' => 'Genel', 'description' => 'Favori İçerikleriniz!'],
            ['name' => 'Beğendiklerim', 'type' => 'Genel', 'description' => 'Beğendiğiniz İçerikler!'],
        ];

        foreach ($fixed_lists_data as $list_data) {
            UserList::create([ 
                'user_id' => $user->id,
                'name' => $list_data['name'],
                'description' => $list_data['description'],
                'type' => $list_data['type'],
                'is_public' => 0,
                'is_fixed' => 1,
            ]);
        }
    }
    
    // Kullanıcı Çıkışı
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}