<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol | The Listoria</title>
</head>
<body>
    <div style="text-align: center; margin-top: 50px;">
        <h2>Yeni Hesap Oluştur</h2>
        
        <form method="POST" action="/public/register">
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
                <label for="name">Adınız ve Soyadınız (Opsiyonel):</label><br>
                <input id="name" type="text" name="name" autofocus>
            </div>
            <br>
            
            <div class="mt-4">
                <label for="username">Kullanıcı Adı:</label><br>
                <input id="username" class="block mt-1 w-full" type="text" name="username" value="{{ old('username') }}" required autofocus />

                @error('username')
                <span class="text-red-600">{{ $message }}</span>
                @enderror
            </div>
            <br>

            <div>
                <label for="email">E-posta:</label><br>
                <input id="email" type="email" name="email" required>
            </div>
            <br>

            <div>
                <label for="password">Şifre:</label><br>
                <input id="password" type="password" name="password" required>
            </div>
            <br>

            <div>
                <label for="password_confirmation">Şifre Tekrarı:</label><br>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            <br>

            <div>
                <button type="submit">Kayıt Ol</button>
            </div>
        </form>

        <p>Zaten hesabınız var mı? <a href="/public/login">Giriş Yap</a></p>
    </div>
</body>
</html>