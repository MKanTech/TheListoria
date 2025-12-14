@extends('layouts.app') 

@section('title', 'Yeni Liste Oluştur | The Listoria')

@section('content')
    <div class="container mx-auto max-w-xl py-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Yeni Liste Oluştur</h2>

        <form method="POST" action="{{ route('lists.store') }}" class="bg-white p-8 rounded-lg shadow-xl">
            @csrf
            
            {{-- Hata Mesajları --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Liste Adı --}}
            <div class="mb-5">
                <label for="name" class="block text-gray-700 font-semibold mb-2">Liste Adı</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Açıklama --}}
            <div class="mb-5">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Açıklama (İsteğe Bağlı)</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>
            
            {{-- Tür --}}
            @php
                $types = ['Genel', 'Film', 'Kitap', 'Dizi', 'Oyun', 'Müzik', 'Yemek', 'Diğer'];
            @endphp

            <div class="mb-5">
                <label for="type" class="block text-gray-700 font-semibold mb-2">Liste Türü</label>
                <select id="type" name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Gizlilik --}}
            <div class="mb-6">
                <label for="is_public" class="block text-gray-700 font-semibold mb-2">Gizlilik Ayarı</label>
                <select id="is_public" name="is_public" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="1" {{ old('is_public') == 1 ? 'selected' : '' }}>Herkese Açık</option>
                    <option value="0" {{ old('is_public') == 0 ? 'selected' : '' }}>Gizli (Sadece Ben Görürüm)</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Sabit listeleriniz her zaman gizlidir.</p>
            </div>
            
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150">
                Listeyi Oluştur
            </button>
        </form>
    </div>
@endsection