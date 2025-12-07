<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>{{ $list->title }} Detayları | The Listoria</title>
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
        <h1>Liste Detayları: {{ $list->title }}</h1>
        <p>Açıklama: {{ $list->description ?? 'Açıklama eklenmedi.' }}</p>
        <p>Tür: {{ $list->list_type }}</p>
        <p>Durum: {{ $list->is_public ? 'Herkese Açık' : 'Gizli' }}</p>
        <p><a href="{{ route('lists.edit', $list) }}">Listeyi Düzenle</a></p>

        <hr style="width: 50%;">

        <h2>Liste Öğeleri</h2>

        @if ($list->items->isEmpty())
        <p>Listenizde henüz öğe yok.</p>
        @else
        <ul>
        @foreach ($list->items as $item)
            <li>
                <div id="view-{{ $item->id }}" style="display: block;">
                    <strong>
                        {{ $item->content }} 
                        @if ($item->release_year)
                            ({{ $item->release_year }})
                        @endif
                    </strong>

                    <button onclick="toggleEditForm({{ $item->id }})">Düzenle</button>

                    <form action="{{ route('lists.items.destroy', ['list' => $list->id, 'item' => $item->id]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Bu öğeyi silmek istediğinizden emin misiniz?');">Sil</button>
                    </form>
                    <form action="{{ route('lists.items.toggle', ['list' => $list->id, 'item' => $item->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" 
                                class="{{ $item->is_completed ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 hover:bg-gray-500' }} text-white py-1 px-3 rounded text-sm">
                            {{ $item->is_completed ? '✔ Tamamlandı' : 'Yapılacak' }}
                        </button>
                    </form>
                </div>

                <div id="edit-{{ $item->id }}" style="display: none; border: 1px solid #ddd; padding: 10px; margin-top: 5px;">
                    <form method="POST" action="{{ route('list_items.update', ['list' => $list->id, 'item' => $item->id]) }}">
                        @csrf
                        @method('PUT') <label for="content-{{ $item->id }}">İçerik:</label>
                        <input id="content-{{ $item->id }}" type="text" name="content" value="{{ old('content', $item->content) }}" required style="width: 250px;">
                        <br>

                        <label for="year-{{ $item->id }}">Yıl (Ops.):</label>
                        <input id="year-{{ $item->id }}" type="text" name="release_year" value="{{ old('release_year', $item->release_year) }}" style="width: 80px;">
                        <br>

                        <button type="submit" style="background-color: #007bff; margin-top: 5px;">Kaydet</button>
                        <button type="button" onclick="toggleEditForm({{ $item->id }})" style="background-color: #6c757d;">İptal</button>
                    </form>
                </div>
            </li>
        @endforeach
        </ul>
    @endif

        <hr style="width: 50%;">

        <h3>Yeni Öğe Ekle</h3>

@if(session('success'))
    <div style="color: green; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('lists.items.store', $list) }}">
    @csrf

    @if ($errors->any())
        <div style="color: red; margin-bottom: 10px;">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div>
        <label for="content">İçerik (Adı):</label><br>
        <input id="content" type="text" name="content" value="{{ old('content') }}" required style="width: 300px; padding: 5px;">
    </div>
    <br>

    <div>
        <label for="release_year">Yayın Yılı (4 Basamak):</label><br>
        <input id="release_year" type="number" name="release_year" value="{{ old('release_year') }}" style="width: 150px; padding: 5px;">
    </div>
    <br>

    <button type="submit">Öğe Ekle</button>
</form>

<hr style="width: 50%; margin-top: 30px;">
        
        <p style="margin-top: 30px;"><a href="{{ route('lists.index') }}">Listelerime Geri Dön</a></p>
    </div>
    <script>
    // Düzenleme formunu gösteren/gizleyen fonksiyon
    function toggleEditForm(itemId) {
        const viewDiv = document.getElementById('view-' + itemId);
        const editDiv = document.getElementById('edit-' + itemId);

        if (editDiv.style.display === 'none') {
            viewDiv.style.display = 'none';
            editDiv.style.display = 'block';
        } else {
            editDiv.style.display = 'none';
            viewDiv.style.display = 'block';
        }
    }
</script>
</body>
</html>
</body>
</html>