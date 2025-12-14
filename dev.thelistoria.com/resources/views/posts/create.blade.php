@extends('layouts.app') 

@section('title', 'Yeni İçerik Ekle | The Listoria')

@section('content')
    <div class="container mx-auto max-w-2xl py-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Yeni İçerik (Post) Ekle</h2>

        <form method="POST" action="{{ route('posts.store') }}" class="bg-white p-8 rounded-lg shadow-xl">
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

            {{-- Başlık --}}
            <div class="mb-5">
                <label for="title" class="block text-gray-700 font-semibold mb-2">Başlık (Dizi/Film/Kitap Adı)</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Açıklama --}}
            <div class="mb-5">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Açıklama/Yorumunuz (İsteğe Bağlı)</label>
                <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>
            
            {{-- Yayın Yılı --}}
            <div class="mb-5">
                <label for="publish_year" class="block text-gray-700 font-semibold mb-2">Yayın/Çıkış Yılı (İsteğe Bağlı)</label>
                <input type="number" id="publish_year" name="publish_year" value="{{ old('publish_year') }}" min="1900" max="{{ date('Y') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <hr class="my-6">
            
            {{-- Listeye Ekleme Seçenekleri --}}
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Listeleme ve Durum</h3>

            {{-- Durum Seçimi --}}
            <div class="mb-5">
                <label for="user_status" class="block text-gray-700 font-semibold mb-2">İçerik Durumu</label>
                <select id="user_status" name="user_status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="Beklemede" {{ old('user_status') == 'Beklemede' ? 'selected' : '' }}>Beklemede (Henüz Başlamadım)</option>
                    <option value="Devam Ediyor" {{ old('user_status') == 'Devam Ediyor' ? 'selected' : '' }}>Devam Ediyor</option>
                    <option value="Tamamlandı" {{ old('user_status') == 'Tamamlandı' ? 'selected' : '' }}>Tamamlandı</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">"Devam Ediyor" veya "Tamamlandı" seçerseniz, ilgili sabit listelere otomatik eklenecektir.</p>
            </div>

            {{-- Manuel Liste Seçimi --}}
            <div class="border p-6 rounded-lg bg-gray-50 mt-6" id="list-management-area">
                
                {{-- DÜZELTME: is_new_list alanını buraya taşıdık. Artık hiçbir zaman gizlenmeyecek/disabled olmayacak. --}}
                <input type="hidden" name="is_new_list" id="is_new_list_field" value="0">
                
                <h3 class="text-xl font-semibold mb-4 text-gray-700">Listeleme ve Kayıt</h3>

                {{-- Seçenek Butonları --}}
                <div class="flex space-x-4 mb-4">
                    <button type="button" onclick="selectListMode('existing')" id="btn-existing" class="px-4 py-2 rounded-lg text-white font-semibold transition duration-150 bg-indigo-600 hover:bg-indigo-700">
                        Mevcut Listelere Ekle
                    </button>
                    <button type="button" onclick="selectListMode('new')" id="btn-new" class="px-4 py-2 rounded-lg text-gray-700 font-semibold transition duration-150 border border-gray-400 bg-white hover:bg-gray-100">
                        + Yeni Liste Oluştur ve Ekle
                    </button>
                </div>

                {{-- 1. Mevcut Listeler Alanı --}}
                <div id="existing-lists-container">
                    <p class="text-sm text-gray-600 mb-3">Bu içeriği hangi mevcut listelerinize eklemek istersiniz? (Birden fazla seçilebilir)</p>
                    <div class="grid grid-cols-2 gap-3 max-h-40 overflow-y-auto p-2 border rounded-lg bg-white">
                        @forelse ($manualLists as $list)
                            <label class="flex items-center space-x-2 text-sm cursor-pointer hover:bg-indigo-50 p-1 rounded">
                                <input type="checkbox" name="list_ids[]" value="{{ $list->id }}" class="form-checkbox h-4 w-4 text-indigo-600 rounded">
                                <span class="text-gray-700">{{ $list->name }} ({{ $list->type }})</span>
                            </label>
                        @empty
                            <p class="text-gray-500 col-span-2">Henüz manuel listeniz yok. Yeni liste oluşturabilirsiniz.</p>
                        @endforelse
                    </div>
                </div>

                {{-- 2. Yeni Liste Oluşturma Alanı (Başlangıçta Gizli) --}}
                <div id="new-list-container" class="hidden border-t pt-4 mt-4">
                    {{-- GİZLİ ALAN BURADAN KALDIRILDI --}}

                    <h3 class="text-xl font-semibold mb-4 text-gray-700">Yeni Liste Detayları</h3>

                    {{-- Yeni Liste Adı --}}
                    <div class="mb-3">
                        <label for="new_list_name" class="block text-gray-700 font-semibold mb-1 text-sm">Liste Adı <span class="text-red-500">*</span></label>
                        <input type="text" id="new_list_name" name="new_list_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500" disabled>
                    </div>

                    {{-- Yeni Liste Türü --}}
                    <div class="mb-3">
                        <label for="new_list_type" class="block text-gray-700 font-semibold mb-1 text-sm">Liste Türü <span class="text-red-500">*</span></label>
                        <select id="new_list_type" name="new_list_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500" disabled>
                            @foreach($types as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Yeni Liste Açıklaması --}}
                    <div class="mb-3">
                        <label for="new_list_description" class="block text-gray-700 font-semibold mb-1 text-sm">Açıklama</label>
                        <textarea id="new_list_description" name="new_list_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500" disabled></textarea>
                    </div>

                    {{-- Yeni Liste Gizlilik --}}
                    <div class="mb-3">
                        <label for="new_list_is_public" class="flex items-center space-x-2 text-sm cursor-pointer">
                            <input type="checkbox" id="new_list_is_public" name="new_list_is_public" value="1" class="form-checkbox h-4 w-4 text-indigo-600 rounded" disabled>
                            <span class="text-gray-700">Listeyi Herkese Açık Yap</span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full mt-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150">
                Gönderiyi Kaydet ve Listele
            </button>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        // Formdaki ilgili alanları seç
        const existingContainer = document.getElementById('existing-lists-container');
        const newListContainer = document.getElementById('new-list-container');
        const isNewListField = document.getElementById('is_new_list_field'); // Artık div dışında
        const existingCheckboxes = document.querySelectorAll('#existing-lists-container input[type="checkbox"]');
        // Yeni liste inputlarının tamamını seçiyoruz (is_new_list field hariç)
        const newListFields = document.querySelectorAll('#new-list-container input, #new-list-container select, #new-list-container textarea'); 
        const btnExisting = document.getElementById('btn-existing');
        const btnNew = document.getElementById('btn-new');

        function selectListMode(mode) {
            if (mode === 'existing') {
                // Mevcut Listeler Modu
                existingContainer.classList.remove('hidden');
                newListContainer.classList.add('hidden');
                isNewListField.value = 0; // Değer 0 olarak ayarlandı

                // Alanları etkinleştirme/devre dışı bırakma
                existingCheckboxes.forEach(cb => cb.disabled = false);
                newListFields.forEach(field => field.disabled = true); // Yeni liste alanları devre dışı

                // Buton stilleri
                btnExisting.classList.remove('bg-white', 'border-gray-400', 'text-gray-700', 'hover:bg-gray-100');
                btnExisting.classList.add('bg-indigo-600', 'text-white');
                btnNew.classList.remove('bg-indigo-600', 'text-white');
                btnNew.classList.add('bg-white', 'border-gray-400', 'text-gray-700', 'hover:bg-gray-100');

            } else if (mode === 'new') {
                // Yeni Liste Oluşturma Modu
                existingContainer.classList.add('hidden');
                newListContainer.classList.remove('hidden');
                isNewListField.value = 1; // Değer 1 olarak ayarlandı

                // Alanları etkinleştirme/devre dışı bırakma
                existingCheckboxes.forEach(cb => cb.disabled = true);
                newListFields.forEach(field => field.disabled = false); // Yeni liste alanları etkin

                // Buton stilleri
                btnNew.classList.remove('bg-white', 'border-gray-400', 'text-gray-700', 'hover:bg-gray-100');
                btnNew.classList.add('bg-indigo-600', 'text-white');
                btnExisting.classList.remove('bg-indigo-600', 'text-white');
                btnExisting.classList.add('bg-white', 'border-gray-400', 'text-gray-700', 'hover:bg-gray-100');
            }
        }

        // Sayfa yüklendiğinde varsayılan modu ayarla
        document.addEventListener('DOMContentLoaded', () => {
             selectListMode('existing');
        });
    </script>
@endsection