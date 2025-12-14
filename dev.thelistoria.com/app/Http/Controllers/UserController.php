<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Kullanıcının profilini ve tüm listelerini gösterir.
     * @param string $username
     */
    public function showProfile($username)
    {
        // Kullanıcıyı kullanıcı adına göre bul
        $user = User::where('username', $username)->firstOrFail();
        
        // Sadece giriş yapmış kullanıcı kendi listelerini görebilir
        $is_owner = Auth::check() && Auth::id() === $user->id;

        if ($is_owner) {
            // Kullanıcının tüm listelerini (sabit + manuel) çek
            $lists = $user->lists()->withCount('posts')->get();
        } else {
            // Başkası bakıyorsa sadece herkese açık listeleri çek
            $lists = $user->lists()
                          ->where('is_public', 1)
                          ->withCount('posts')
                          ->get();
        }

        // view'e hem kullanıcıyı hem de listeleri gönderiyoruz
        return view('profile.show', compact('user', 'lists', 'is_owner'));
    }
}