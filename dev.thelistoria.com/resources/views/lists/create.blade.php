<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Liste Oluştur | The Listoria</title>
</head>
<body>
    <div style="text-align: right; padding: 10px; border-bottom: 1px solid #ccc;">
        <span>Hoş Geldiniz, {{ Auth::user()->name }}!</span>
        <form method="POST" action="/public/logout" style="display:inline;">
            @csrf
            <button type="submit">Çıkış Yap</button>
        </form>
    </div>

    <div style="text-align: center; margin-top: 50px;">
        <h1>Yeni Liste Oluştur</h1>
        
        <form method="POST" action="{{ route('lists.store') }}">
            @csrf
            
            @if ($errors->any())
                <div style="color: red; margin-bottom: 10px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="title">Liste Başlığı:</label><br>
                <input id="title" type="text" name="title" value="{{ old('title') }}" required>
            </div>
            <br>

            <div>
                <label for="description">Açıklama (Opsiyonel):</label><br>
                <textarea id="description" name="description">{{ old('description') }}</textarea>
            </div>
            <br>

            <div>
                <label for="is_public">
                    <input id="is_public" type="checkbox" name="is_public">
                    Herkese Açık Yap
                </label>
            </div>
            <br>

            <button type="submit">Listeyi Kaydet</button>
        </form>

        <p><a href="{{ route('lists.index') }}">Listelerime Geri Dön</a></p>
    </div>
</body>
</html>