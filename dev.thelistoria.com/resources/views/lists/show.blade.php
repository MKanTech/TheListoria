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
        <p>Durum: {{ $list->is_public ? 'Herkese Açık' : 'Gizli' }}</p>

        <hr style="width: 50%;">

        <h2>Liste Öğeleri (Items)</h2>
        
        @if($items->isEmpty())
            <p>Bu listede henüz hiçbir öğe yok.</p>
        @else
            <ul style="list-style-type: none; padding: 0;">
                @foreach($items as $item)
                    <li style="text-decoration: {{ $item->is_completed ? 'line-through' : 'none' }}; margin: 5px 0;">
    <form method="POST" action="{{ route('lists.items.toggle', ['list' => $list->id, 'item' => $item->id]) }}" style="display:inline;">
        @csrf
        {{ $item->content }} 

        <button type="submit" style="margin-left: 10px; cursor: pointer;">
            {{ $item->is_completed ? 'Geri Al' : 'Tamamla' }}
        </button>
    </form>
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
        <label for="content">Yeni Öğe:</label><br>
        <input id="content" type="text" name="content" required style="width: 300px; padding: 5px;">
    </div>
    <br>

    <button type="submit">Öğe Ekle</button>
</form>
        
        <p style="margin-top: 30px;"><a href="{{ route('lists.index') }}">Listelerime Geri Dön</a></p>
    </div>
</body>
</html>