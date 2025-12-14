@extends('layouts.app') 

@section('title', $post->title . ' Detayı | The Listoria')

@section('content')
    <div class="container mx-auto max-w-3xl py-8">
        
        {{-- Başlık ve Aksiyonlar --}}
        <div class="bg-white p-6 rounded-lg shadow-xl mb-8">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-2">{{ $post->title }}</h1>
            <p class="text-gray-600 text-lg mb-4">{{ $post->description }}</p>
            
            <div class="text-sm text-gray-500 space-x-4 border-t pt-4 mt-4">
                @if($post->publish_year)
                    <span>Yayın Yılı: {{ $post->publish_year }}</span>
                @endif
                <span>Oluşturulma: {{ $post->created_at->format('d M Y') }}</span>
                
                {{-- Postun Ana Durumu (Herhangi bir liste pivotundaki durum) --}}
                @php
                    $mainStatus = $post->lists()->first()->pivot->user_status ?? 'Durum Yok';
                @endphp
                <span class="font-bold px-2 py-1 rounded 
                    @if($mainStatus == 'Tamamlandı') bg-green-200 text-green-800
                    @elseif($mainStatus == 'Devam Ediyor') bg-blue-200 text-blue-800
                    @else bg-yellow-200 text-yellow-800
                    @endif">
                    Aktif Durum: {{ $mainStatus }}
                </span>
            </div>
            
            <div class="flex space-x-3 mt-6 border-t pt-4">
                {{-- Düzenle Butonu --}}
                <a href="{{ route('posts.edit', $post->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm transition duration-150">
                    İçeriği Düzenle
                </a>
                
                {{-- Silme Formu --}}
                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('UYARI: Bu içerik ve tüm liste bağlantıları kalıcı olarak silinecektir. Emin misiniz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm transition duration-150">
                        Kalıcı Olarak Sil
                    </button>
                </form>
                
                <a href="{{ route('profile.show', ['username' => Auth::user()->username]) }}" class="text-gray-600 hover:text-gray-800 self-center text-sm">
                    &larr; Tüm Listelerime Dön
                </a>
            </div>
        </div>

        {{-- Bağlı Olduğu Listeler --}}
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Bağlı Olduğu Listeler ({{ $attachedLists->count() }})</h2>
        
        <div class="space-y-3">
            @if($attachedLists->isEmpty())
                 <p class="text-gray-500">Bu içerik şu anda hiçbir listeye bağlı değil.</p>
            @else
                @foreach ($attachedLists as $list)
                    <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center border">
                        <div>
                            <p class="text-lg font-semibold text-indigo-700">
                                <a href="{{ route('lists.show', $list->id) }}" class="hover:underline">{{ $list->name }}</a>
                            </p>
                            <span class="text-xs text-gray-500">
                                Türü: <span class="font-bold text-gray-700">{{ $list->type }}</span>
                                <br/> {{ $list->is_fixed ? 'Sabit Liste' : 'Manuel Liste' }}
                                ({{ $list->is_public ? 'Herkese Açık' : 'Gizli' }})
                            </span>
<!--
                            <span class="text-xs text-gray-500">
                                {{ $list->is_fixed ? 'Sabit Liste' : 'Manuel Liste' }}
                                ({{ $list->is_public ? 'Herkese Açık' : 'Gizli' }})
                            </span>
-->
                        </div>
                        
                        <span class="text-sm font-medium px-3 py-1 rounded-full 
                            @if($list->pivot->user_status == 'Tamamlandı') bg-green-100 text-green-700
                            @elseif($list->pivot->user_status == 'Devam Ediyor') bg-blue-100 text-blue-700
                            @else bg-yellow-100 text-yellow-700
                            @endif">
                            Durum: {{ $list->pivot->user_status }}
                        </span>
                    </div>
                @endforeach
            @endif
        </div>
        
    </div>
@endsection