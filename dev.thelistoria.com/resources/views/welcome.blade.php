<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>The Listoria | Topluluk Arşivi</title>
    <style>
        body { font-family: sans-serif; }
        .nav { text-align: right; padding: 10px; border-bottom: 1px solid #ccc; }
        .content { text-align: center; margin-top: 50px; }
    </style>
</head>
<body>

    <div class="nav">
        @auth
        <div style="margin-top: 1px;">
        <span>Hoş Geldiniz, **{{ Auth::user()->username }}!**</span>
        
        @auth
        <a href="{{ route('items.create') }}" 
           class="py-2 px-4 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition duration-150 mr-2">
            ➕ İçerik Ekle
        </a>
        @endauth
        
        <a href="{{ route('lists.index') }}" style="display: inline-block; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 15px;">
            Listelerime Git
        </a>
        
        <form method="POST" action="/public/logout" style="display:inline;">
            @csrf
            <button type="submit" style="padding: 10px 15px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Çıkış Yap
            </button>
        </form>
        </div>
        @else
        <a href="{{ route('login') }}">Giriş Yap</a>
        <a href="{{ route('register') }}" style="margin-left: 10px;">Kayıt Ol</a>
    @endauth
    </div>

    <div class="content">
        <h1>The Listoria Projesi</h1>
        <p>Geliştirme Ortamı Başarıyla Kuruldu.</p>
    </div>
    
</body>
</html>