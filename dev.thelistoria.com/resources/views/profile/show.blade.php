@extends('layouts.app') 

@section('title', '@' . $user->username . ' Profili | The Listoria')

@section('content')
    <div class="container mx-auto py-8">
        
        {{-- Profil Başlığı --}}
        <div class="bg-white p-6 rounded-lg shadow-xl mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">@ {{ $user->username }}</h1>
            <p class="text-gray-600 text-lg">{{ $user->name }}</p>
            
            @if($is_owner)
                <div class="mt-4 flex space-x-3">
                    <a href="{{ route('posts.create') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded transition">
                        + Yeni İçerik Ekle
                    </a>
                    <a href="{{ route('lists.create') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded transition">
                        + Yeni Liste Oluştur
                    </a>
                </div>
            @endif
        </div>

        {{-- Listeler --}}
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            {{ $is_owner ? 'Listelerim' : 'Herkese Açık Listeleri' }} ({{ $lists->count() }})
        </h2>
        
        @if($lists->isEmpty())
            <p class="text-gray-500">Henüz listesi bulunmamaktadır.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($lists as $list)
                    <div class="bg-white p-5 rounded-lg shadow border-l-4 {{ $list->is_fixed ? 'border-red-500' : 'border-indigo-500' }}">
                        <h3 class="text-xl font-semibold mb-2 flex justify-between items-start">
                            <span>{{ $list->name }}</span>
                            <span class="text-sm font-normal bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full ml-2">
                                {{ $list->posts_count }} İçerik
                            </span>
                        </h3>
                        <p class="text-gray-600 text-sm mb-3 h-10 overflow-hidden">{{ $list->description }}</p>
                           
                        <div class="text-xs text-gray-500 mb-1 mt-2 flex justify-between">
                            <span class="font-medium text-indigo-700">{{ $list->type }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <div>
                                @if($list->is_fixed)
                                    <span class="text-red-500 font-medium">Sabit Liste</span>
                                @else
                                    <span class="{{ $list->is_public ? 'text-green-500' : 'text-orange-500' }} font-medium">
                                        {{ $list->is_public ? 'Herkese Açık' : 'Gizli' }}
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('lists.show', $list->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                Detayları Gör &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
    </div>
@endsection