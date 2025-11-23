<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Liste Düzenle | {{ $list->title }}</title>
    <style>
        /* Basit stil, create.blade.php'deki stille aynı olabilir */
        body { text-align: center; font-family: sans-serif; }
        form { display: inline-block; text-align: left; border: 1px solid #ccc; padding: 20px; border-radius: 5px; margin-top: 20px; }
        input[type="text"], textarea { width: 300px; padding: 5px; margin-top: 5px; border: 1px solid #ddd; }
        select { padding: 5px; margin-top: 5px; border: 1px solid #ddd; }
        button { padding: 10px 15px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <h1>"{{ $list->title }}" Listesini Düzenle</h1>

    <form method="POST" action="{{ route('lists.update', $list) }}">
        @csrf
        @method('PUT') 

        @if ($errors->any())
            <div style="color: red; margin-bottom: 10px;">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div>
            <label for="title">Liste Başlığı:</label><br>
            <input id="title" type="text" name="title" value="{{ old('title', $list->title) }}" required>
        </div>
        <br>

        <div>
            <label for="description">Açıklama (Opsiyonel):</label><br>
            <textarea id="description" name="description">{{ old('description', $list->description) }}</textarea>
        </div>
        <br>

        <div>
            <label for="list_type">Listenin Türü:</label><br>
            <select id="list_type" name="list_type" required style="padding: 5px;">
                @foreach(['Diğer', 'Film', 'Kitap', 'Dizi', 'Oyun'] as $type)
                    <option value="{{ $type }}" {{ old('list_type', $list->list_type) == $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <label for="is_public">Gizlilik:</label><br>
            <div>
                <label for="is_public_checkbox">
                    <input id="is_public_checkbox" type="checkbox" name="is_public" {{ old('is_public', $list->is_public) ? 'checked' : '' }}>
                    Herkese Açık Yap
                </label>
            </div>
        </div>
        <br>

        <button type="submit">Değişiklikleri Kaydet</button>
    </form>

    <p style="margin-top: 20px;"><a href="{{ route('lists.show', $list) }}">Geri Dön</a></p>
</body>
</html>