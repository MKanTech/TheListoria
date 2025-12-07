<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni İçerik Ekle - TheListoria</title>
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased">
    <div class="relative min-h-screen bg-gray-100 dark:bg-gray-900 selection:bg-red-500 selection:text-white">

        @include('welcome') 

        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Yeni İçerik Ekle
                </h2>
            </div>
        </header>

        <main class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
        
                    <h3 class="text-2xl font-bold mb-6 text-gray-800">Yeni İçerik Ekle</h3>
        
                    <form method="POST" action="{{ route('items.store') }}">
                        <div class="mb-6 flex justify-center space-x-4">
                            <button type="button" onclick="toggleFormSections('EXISTING')" 
                                    id="btn-existing"
                                    class="py-2 px-4 rounded-lg text-sm font-semibold transition duration-150 border-2 border-indigo-600 bg-indigo-600 text-white">
                                Mevcut Listeye Ekle
                            </button>
                            <button type="button" onclick="toggleFormSections('NEW')" 
                                    id="btn-new"
                                    class="py-2 px-4 rounded-lg text-sm font-semibold transition duration-150 border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50">
                                Yeni Liste Oluştur
                            </button>
                        </div>
                        <hr class="my-6">
                        @csrf
                        @if ($errors->any())
                            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                                Lütfen hataları kontrol edin.
                            </div>
                        @endif
        
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700">İçerik Adı</label>
                            <input id="content" type="text" name="content" value="{{ old('content') }}" required autofocus class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                            @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
        
                        <div class="mb-6">
                            <label for="release_year" class="block text-sm font-medium text-gray-700">Yayın Yılı (Opsiyonel)</label>
                            <input id="release_year" type="number" name="release_year" value="{{ old('release_year') }}" placeholder="örn: 2024" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                            @error('release_year') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
        
                        <div class="mb-6">
                            <label for="list_id" class="block text-sm font-medium text-gray-700">Mevcut Listelerinizden Seçin</label>
                            <select id="list_id" name="list_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                                <option value="">-- Lütfen bir liste seçin --</option>
                                @foreach ($userLists as $list)
                                    <option value="{{ $list->id }}" {{ old('list_id') == $list->id ? 'selected' : '' }}>
                                        {{ $list->title }} ({{ $list->list_type }})
                                    </option>
                                @endforeach
                            </select>
                            @error('list_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
        
                        <div class="mb-6 border p-4 rounded-lg bg-gray-50">
                            <h4 class="font-bold text-gray-700 mb-3">Yeni Liste Oluşturun</h4>
                        
                            <div class="mb-4">
                                <label for="new_list_title" class="block text-sm font-medium text-gray-700">Liste Adı</label>
                                <input id="new_list_title" type="text" name="new_list_title" value="{{ old('new_list_title') }}" placeholder="Yeni Listenin Adı" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                                @error('new_list_title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        
                            <div class="mb-4">
                                <label for="new_list_type" class="block text-sm font-medium text-gray-700">Liste Türü</label>
                                <select id="new_list_type" name="new_list_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">
                                    <option value="Film">Film</option>
                                    <option value="Dizi">Dizi</option>
                                    <option value="Kitap">Kitap</option>
                                </select>
                                @error('new_list_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        
                            <div class="mb-4">
                                <label for="new_list_description" class="block text-sm font-medium text-gray-700">Açıklama (Ops.)</label>
                                <textarea id="new_list_description" name="new_list_description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2">{{ old('new_list_description') }}</textarea>
                                @error('new_list_description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                        
                            <div class="mb-4 flex items-center">
                                <input id="new_list_public" type="checkbox" name="new_list_public" value="1" {{ old('new_list_public') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="new_list_public" class="ml-2 block text-sm font-medium text-gray-700">Herkesle Paylaşılabilir (Açık)</label>
                            </div>
                        
                        </div>
        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150">
                                İçeriği Kaydet
                            </button>
                        </div>
                        <script>
                        const listSelect = document.getElementById('list_id');
                        const existingListDiv = listSelect.closest('.mb-6'); // Mevcut liste seçimi div'i
                    
                        const newListTitle = document.getElementById('new_list_title');
                        const newListBox = newListTitle.closest('.bg-gray-50'); // Yeni liste oluşturma div'i
                    
                        const btnExisting = document.getElementById('btn-existing');
                        const btnNew = document.getElementById('btn-new');
                    
                        function toggleFormSections(mode) {
                            if (mode === 'NEW') {
                                existingListDiv.style.display = 'none';
                                newListBox.style.display = 'block';
                                listSelect.value = ''; // Mevcut listeyi sıfırla
                                btnNew.classList.remove('text-indigo-600', 'hover:bg-indigo-50');
                                btnNew.classList.add('bg-indigo-600', 'text-white');
                                btnExisting.classList.remove('bg-indigo-600', 'text-white');
                                btnExisting.classList.add('text-indigo-600', 'hover:bg-indigo-50');
                    
                            } else if (mode === 'EXISTING') {
                                existingListDiv.style.display = 'block';
                                newListBox.style.display = 'none';
                                newListTitle.value = ''; // Yeni liste adını sıfırla
                                btnExisting.classList.remove('text-indigo-600', 'hover:bg-indigo-50');
                                btnExisting.classList.add('bg-indigo-600', 'text-white');
                                btnNew.classList.remove('bg-indigo-600', 'text-white');
                                btnNew.classList.add('text-indigo-600', 'hover:bg-indigo-50');
                            }
                        }
                    
                        // Başlangıçta sadece mevcut listeleri göster
                        toggleFormSections('EXISTING'); 
                    </script>
                    </form>
        
                </div>
            </div>
        </main>
    </div>
</body>
</html>