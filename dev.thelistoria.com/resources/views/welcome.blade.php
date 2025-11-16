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
            <span>Hoş Geldiniz, {{ Auth::user()->name }}!</span>
            <form method="POST" action="/public/logout" style="display:inline;">
                @csrf
                <button type="submit">Çıkış Yap</button>
            </form>
        @else
            <a href="/public/login">Giriş Yap</a> | 
            <a href="/public/register">Kayıt Ol</a>
        @endauth
    </div>

    <div class="content">
        <h1>The Listoria Projesi</h1>
        <p>Geliştirme Ortamı Başarıyla Kuruldu.</p>
    </div>
    
</body>
</html>