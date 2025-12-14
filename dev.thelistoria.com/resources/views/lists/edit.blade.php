@extends('layouts.app') 

@section('title', $list->name . ' Düzenle | The Listoria')

@section('content')
    <div class="container mx-auto max-w-xl py-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Listeyi Düzenle: {{ $list->name }}</h2>

        {{-- Güncelleme Metodu: PUT --}}
        <form method="POST" action="{{ route('lists.update', $list->id) }}" class="bg-white p-8 rounded-lg shadow-xl">
            @csrf
            @method('PUT')
            
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
                <input type="text" id="name" name="name" value="{{ old('name', $list->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Açıklama --}}
            <div class="mb-5">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Açıklama (İsteğe Bağlı)</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $list->description) }}</textarea>
            </div>
            
            {{-- Tür --}}
            @php
                $types = ['Genel', 'Film', 'Kitap', 'Dizi', 'Oyun', 'Müzik', 'Yemek', 'Diğer'];
            @endphp

            <div class="mb-5">
                <label for="type" class="block text-gray-700 font-semibold mb-2">Liste Türü</label>
                <select id="type" name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type', $list->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Gizlilik --}}
            <div class="mb-6">
                <label for="is_public" class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" id="is_public" name="is_public" value="1" 
                           class="form-checkbox h-5 w-5 text-indigo-600 rounded" 
                           {{ old('is_public', $list->is_public) ? 'checked' : '' }}>
                    <span class="text-gray-700 font-semibold">Listeyi Herkese Açık Yap</span>
                </label>
                <p class="text-sm text-gray-500 mt-1">İşaretlerseniz, bu liste keşfet sayfasında görünebilir.</p>
            </div>
            
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150">
                Listeyi Güncelle
            </button>
        </form>
    </div>
@endsection