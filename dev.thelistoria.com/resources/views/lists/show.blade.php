@extends('layouts.app') 

@section('title', $list->name . ' Listesi | The Listoria')

@section('content')
    <div class="container mx-auto py-8">
        
        {{-- Liste Başlığı ve Aksiyonlar --}}
        <div class="bg-white p-6 rounded-lg shadow-xl mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $list->name }}</h1>
                    <p class="text-gray-600 text-lg mb-2">{{ $list->description }}</p>
                    <div class="text-sm text-gray-500 space-x-3">
                        <span>{{ $posts->total() }} İçerik</span>
                        @if(!$list->is_fixed)
                            <span class="{{ $list->is_public ? 'text-green-500' : 'text-orange-500' }} font-medium">
                                {{ $list->is_public ? 'Herkese Açık' : 'Gizli Liste' }}
                            </span>
                        @else
                            <span class="text-red-500 font-medium">Sabit Liste</span>
                        @endif
                        <span>| {{ $list->type }}</span>
                    </div>
                </div>

                @if($is_owner)
                    <div class="flex space-x-3 mt-4">
                        {{-- SADECE MANUEL LİSTELER İÇİN DÜZENLEME BUTONU --}}
                        @if(!$list->is_fixed)
                            <a href="{{ route('lists.edit', $list->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm transition">
                                Listeyi Düzenle
                            </a>
                        @endif

                        {{-- SİLME BUTONU (Zaten ekli olmalıydı) --}}
                        <form action="{{ route('lists.destroy', $list->id) }}" method="POST" onsubmit="return confirm('Bu listeyi ve tüm içerik ilişkilerini silmek istediğinizden emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm transition">
                                Listeyi Sil
                            </button>
                        </form>
                    </div>
                @endif
            </div>
            
            <a href="{{ route('profile.show', ['username' => $list->user->username]) }}" class="mt-4 inline-block text-indigo-600 hover:underline text-sm">
                &larr; Tüm Listelerime Dön
            </a>
        </div>

        {{-- İçerikler (Postlar) --}}
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Listedeki İçerikler</h2>
        
        @if($posts->isEmpty())
            <p class="text-gray-500">Bu listede henüz içerik bulunmamaktadır.</p>
            @if($is_owner)
                <a href="{{ route('posts.create') }}" class="text-indigo-600 hover:underline">Şimdi bir içerik ekle!</a>
            @endif
        @else
            <div class="space-y-4">
                @foreach ($posts as $post)
                    <div class="bg-white p-5 rounded-lg shadow flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">{{ $post->title }}</h3>
                            <p class="text-gray-600 text-sm mt-1">
                                {{ $post->description ? Str::limit($post->description, 100) : 'Açıklama yok.' }}
                            </p>
                            <span class="text-xs text-gray-500 mt-2 block">
                                Yıl: {{ $post->publish_year ?? 'Bilinmiyor' }}
                            </span>
                        </div>
                        
                        <div class="text-right flex items-center space-x-4">
                            {{-- Durum Bilgisi (Pivot Tablodan Gelir) --}}
                            <span class="text-sm font-medium px-3 py-1 rounded-full 
                                @if($post->pivot->user_status == 'Tamamlandı') bg-green-100 text-green-700
                                @elseif($post->pivot->user_status == 'Devam Ediyor') bg-blue-100 text-blue-700
                                @else bg-yellow-100 text-yellow-700
                                @endif">
                                Durum: {{ $post->pivot->user_status }}
                            </span>
                            
                            {{-- SADECE LİSTE SAHİBİYSE VE MANUEL LİSTEYSE ÇIKARMA BUTONUNU GÖSTER --}}
                            @if($is_owner)
                                @if(!$list->is_fixed)
                                    <form action="{{ route('lists.removePost', ['list' => $list->id, 'postId' => $post->id]) }}" method="POST" onsubmit="return confirm('Bu içeriği listeden çıkarmak istediğinizden emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-sm transition">
                                            Listeden Çıkar
                                        </button>
                                    </form>
                                @endif
                            
                            {{-- İçerik Detay/Düzenleme Linki --}}
                            @if($is_owner)
                                <a href="{{ route('posts.show', $post->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded text-sm transition">
                                        Detaylar
                                </a>
                                @else 
                            {{-- Başkasına ait listede sadece detay gösterilebilir --}}
                                <a href="{{ route('posts.show', $post->id) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded text-sm transition">
                                    Detaylar
                                </a>
                            @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $posts->links() }} {{-- Sayfalama Linkleri --}}
            </div>
        @endif
        
    </div>
@endsection