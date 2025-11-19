<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Listelerim | The Listoria</title>
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
        <h1>Listelerim</h1>
        <p><a href="{{ route('lists.create') }}">Yeni Liste Oluştur</a></p>

        @if(session('success'))
            <div style="color: green; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if($lists->isEmpty())
            <p>Henüz bir listeniz yok. Hadi bir tane oluşturalım!</p>
        @else
            <table border="1" cellpadding="10" style="margin: 20px auto;">
                <thead>
                    <tr>
                        <th>Başlık</th>
                        <th>Açıklama</th>
                        <th>Durum</th>
                        <th>Oluşturma Tarihi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lists as $list)
                        <tr>
                            <td><a href="{{ route('lists.show', $list) }}">{{ $list->title }}</a></td>
                            <td>{{ $list->description ?? 'Yok' }}</td>
                            <td>{{ $list->is_public ? 'Herkese Açık' : 'Gizli' }}</td>
                            <td>{{ $list->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        
        <p><a href="{{ route('home') }}">Ana Sayfaya Dön</a></p>
    </div>
</body>
</html>