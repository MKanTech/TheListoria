<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap | The Listoria</title>
</head>
<body>
    <div style="text-align: center; margin-top: 50px;">
        <h2>Giriş Yap</h2>
        
        <form method="POST" action="/public/login">
            @csrf @if ($errors->any())
                <div style="color: red;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="email">E-posta:</label><br>
                <input id="email" type="email" name="email" required autofocus>
            </div>
            <br>

            <div>
                <label for="password">Şifre:</label><br>
                <input id="password" type="password" name="password" required>
            </div>
            <br>

            <div>
                <label for="remember_me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>Beni Hatırla</span>
                </label>
            </div>
            <br>

            <div>
                <button type="submit">Giriş Yap</button>
            </div>
        </form>

        <p>Hesabınız yok mu? <a href="/public/register">Kayıt Ol</a></p>
    </div>
</body>
</html>