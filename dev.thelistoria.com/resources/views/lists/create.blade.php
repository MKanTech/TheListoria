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
                <label for="list_type">Listenin Türü:</label><br>
                <select id="list_type" name="list_type" required style="padding: 5px;">
                    <option value="Diğer" {{ old('list_type') == 'Diğer' ? 'selected' : '' }}>Diğer (Varsayılan)</option>
                    <option value="Film" {{ old('list_type') == 'Film' ? 'selected' : '' }}>Film</option>
                    <option value="Kitap" {{ old('list_type') == 'Kitap' ? 'selected' : '' }}>Kitap</option>
                    <option value="Dizi" {{ old('list_type') == 'Dizi' ? 'selected' : '' }}>Dizi</option>
                    <option value="Oyun" {{ old('list_type') == 'Oyun' ? 'selected' : '' }}>Oyun</option>
                </select>
            </div>
            <br>

            <div>
                <label for="is_public">Gizlilik:</label><br>
                    <div>
                        <label for="is_public_checkbox">
                            <input id="is_public_checkbox" type="checkbox" name="is_public"> 
                    Herkese Açık Yap
                        </label>
                    </div>
            </div>
            <br>
        
            <button type="submit">Listeyi Kaydet</button>
        </form>
                
                        <p><a href="{{ route('lists.index') }}">Listelerime Geri Dön</a></p>
                </div>
</body>
</html>