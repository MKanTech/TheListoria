@extends('layouts.app') 

@section('title', 'İçerik Düzenle: ' . $post->title)

@section('content')
    <div class="container mx-auto max-w-2xl py-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">İçeriği Düzenle: {{ $post->title }}</h2>

        {{-- Güncelleme Formu --}}
        <form method="POST" action="{{ route('posts.update', $post) }}" class="bg-white p-8 rounded-lg shadow-xl">
            @csrf
            @method('PUT') {{-- Güncelleme için PUT metodu kullanılır --}}
            
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
                <label for="title" class="block text-gray-700 font-semibold mb-2">Başlık</label>
                <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Açıklama --}}
            <div class="mb-5">
                <label for="description" class="block text-gray-700 font-semibold mb-2">Açıklama/Yorumunuz</label>
                <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $post->description) }}</textarea>
            </div>
            
            {{-- Yayın Yılı --}}
            <div class="mb-5">
                <label for="publish_year" class="block text-gray-700 font-semibold mb-2">Yayın/Çıkış Yılı</label>
                <input type="number" id="publish_year" name="publish_year" value="{{ old('publish_year', $post->publish_year) }}" min="1900" max="{{ date('Y') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <hr class="my-6">
            
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Listeleme ve Durum Güncellemesi</h3>

            {{-- Durum Seçimi --}}
            <div class="mb-5">
                <label for="user_status" class="block text-gray-700 font-semibold mb-2">İçerik Durumu</label>
                <select id="user_status" name="user_status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    {{-- currentStatus değişkenini Controller'da çekmiştik --}}
                    <option value="Beklemede" {{ old('user_status', $currentStatus) == 'Beklemede' ? 'selected' : '' }}>Beklemede (Henüz Başlamadım)</option>
                    <option value="Devam Ediyor" {{ old('user_status', $currentStatus) == 'Devam Ediyor' ? 'selected' : '' }}>Devam Ediyor</option>
                    <option value="Tamamlandı" {{ old('user_status', $currentStatus) == 'Tamamlandı' ? 'selected' : '' }}>Tamamlandı</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Bu durum, sabit listelerinizdeki konumunu güncelleyecektir.</p>
            </div>

            {{-- Manuel Liste Yönetimi --}}
            <div class="border p-6 rounded-lg bg-gray-50 mt-6" id="list-management-area-edit">
                
                {{-- Güncelleme için Yeni Liste Oluşturma Bayrağı (Başlangıçta 0) --}}
                {{-- Not: Bu, Post Ekleme'deki is_new_list'ten farklı bir isim (should_create_new_list) alacak, çünkü güncellemede mantık farklı. --}}
                <input type="hidden" name="should_create_new_list" id="should_create_new_list_field" value="0"> 

                <h3 class="text-xl font-semibold mb-4 text-gray-700">Listelere Bağlantıları Yönetin</h3>

                <div class="flex space-x-4 mb-4">
                    {{-- Mevcut Listeler Modu (Varsayılan olarak açık) --}}
                    <button type="button" id="btn-existing-edit" class="px-4 py-2 rounded-lg text-white font-semibold transition duration-150 bg-indigo-600 hover:bg-indigo-700">
                        Mevcut Listeleri Düzenle
                    </button>
                    {{-- Yeni Liste Oluşturma Modu --}}
                    <button type="button" id="btn-new-edit" class="px-4 py-2 rounded-lg text-gray-700 font-semibold transition duration-150 border border-gray-400 bg-white hover:bg-gray-100">
                        + Yeni Liste Oluştur ve Ekle
                    </button>
                </div>

                {{-- 1. Mevcut Listeler Alanı --}}
                <div id="existing-lists-container-edit">
                    <p class="text-sm text-gray-600 mb-3">İçeriğin bağlı olmasını istediğiniz listeleri seçin/seçimi kaldırın:</p>
                    <div class="grid grid-cols-2 gap-3 max-h-40 overflow-y-auto p-2 border rounded-lg bg-white">
                        @forelse ($manualLists as $list) {{-- Düzeltme: $lists yerine $manualLists kullanıldı --}}
                            <label class="flex items-center space-x-2 text-sm cursor-pointer hover:bg-indigo-50 p-1 rounded">
                                <input 
                                    type="checkbox" 
                                    name="list_ids[]" 
                                    value="{{ $list->id }}" 
                                    class="form-checkbox h-4 w-4 text-indigo-600 rounded"
                                    {{ in_array($list->id, old('list_ids', $postListIds)) ? 'checked' : '' }} {{-- Seçili listeler kontrol ediliyor --}}
                                >
                                <span class="text-gray-700">{{ $list->name }} ({{ $list->type }})</span>
                            </label>
                        @empty
                            <p class="text-gray-500 col-span-2">Henüz manuel listeniz yok.</p>
                        @endforelse
                    </div>
                </div>

                {{-- 2. Yeni Liste Oluşturma Alanı (Başlangıçta Gizli) --}}
                <div id="new-list-container-edit" class="hidden border-t pt-4 mt-4">
                    <h3 class="text-xl font-semibold mb-4 text-gray-700">Yeni Liste Detayları (İsteğe Bağlı Ekleme)</h3>

                    {{-- Yeni Liste Adı --}}
                    <div class="mb-3">
                        <label for="new_list_name" class="block text-gray-700 font-semibold mb-1 text-sm">Liste Adı <span class="text-red-500">*</span></label>
                        <input type="text" id="new_list_name_edit" name="new_list_name" value="{{ old('new_list_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500" disabled>
                    </div>

                    {{-- Yeni Liste Türü --}}
                    <div class="mb-3">
                        <label for="new_list_type" class="block text-gray-700 font-semibold mb-1 text-sm">Liste Türü <span class="text-red-500">*</span></label>
                        <select id="new_list_type_edit" name="new_list_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500" disabled>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('new_list_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Yeni Liste Açıklaması --}}
                    <div class="mb-3">
                        <label for="new_list_description" class="block text-gray-700 font-semibold mb-1 text-sm">Açıklama</label>
                        <textarea id="new_list_description_edit" name="new_list_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500" disabled>{{ old('new_list_description') }}</textarea>
                    </div>

                    {{-- Yeni Liste Gizlilik --}}
                    <div class="mb-3">
                        <label for="new_list_is_public_edit" class="flex items-center space-x-2 text-sm cursor-pointer">
                            <input type="checkbox" id="new_list_is_public_edit" name="new_list_is_public" value="1" {{ old('new_list_is_public') ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-indigo-600 rounded" disabled>
                            <span class="text-gray-700">Listeyi Herkese Açık Yap</span>
                        </label>
                    </div>
                    <p class="text-sm text-red-500 mt-2">NOT: Yeni liste oluşturma, mevcut listelerden seçiminizi etkilemez. Her ikisi de işlenecektir.</p>
                </div>
            </div> {{-- list-management-area-edit sonu --}}


            <button type="submit" class="w-full mt-6 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150">
                İçeriği Güncelle
            </button>
        </form>

        {{-- Silme Formu --}}
        <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Bu içeriği kalıcı olarak silmek istediğinizden emin misiniz?');" class="mt-4">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-150">
                İçeriği Sil
            </button>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        // DOM Elementlerini Seçme
        const existingContainerEdit = document.getElementById('existing-lists-container-edit');
        const newListContainerEdit = document.getElementById('new-list-container-edit');
        const shouldCreateNewListField = document.getElementById('should_create_new_list_field');
        const btnExistingEdit = document.getElementById('btn-existing-edit');
        const btnNewEdit = document.getElementById('btn-new-edit');
        const newListFieldsEdit = document.querySelectorAll('#new-list-container-edit input, #new-list-container-edit select, #new-list-container-edit textarea');

        // Durumu Yöneten Fonksiyon
        function toggleNewListCreation(isNewModeActive) {
            if (isNewModeActive) {
                // Yeni Liste Modu AKTİF
                newListContainerEdit.classList.remove('hidden');
                shouldCreateNewListField.value = 1;

                newListFieldsEdit.forEach(field => field.disabled = false);

                btnNewEdit.classList.remove('bg-white', 'border-gray-400', 'text-gray-700', 'hover:bg-gray-100');
                btnNewEdit.classList.add('bg-indigo-600', 'text-white');
            } else {
                // Yeni Liste Modu İNAKTİF
                newListContainerEdit.classList.add('hidden');
                shouldCreateNewListField.value = 0;

                newListFieldsEdit.forEach(field => field.disabled = true);

                btnNewEdit.classList.remove('bg-indigo-600', 'text-white');
                btnNewEdit.classList.add('bg-white', 'border-gray-400', 'text-gray-700', 'hover:bg-gray-100');
            }
        }

        // Event Listener'lar
        btnNewEdit.addEventListener('click', () => {
            // Butona her tıklandığında mevcut durumu tersine çevir
            const currentState = shouldCreateNewListField.value === '1';
            toggleNewListCreation(!currentState);
        });

        // Mevcut Listeleri Düzenle butonu, sadece Yeni Liste Oluşturma butonuyla çakışmayacak bir stil değişikliği yapar
        btnExistingEdit.addEventListener('click', () => {
            // Sadece Mevcut Listeleri Düzenle butonuna tıklamak, Yeni Liste Oluşturma modunu kapatır
             toggleNewListCreation(false); 
        });


        // Sayfa yüklendiğinde (Varsayılan: Yeni Liste Oluşturma inaktif)
        document.addEventListener('DOMContentLoaded', () => {
             toggleNewListCreation(false);
        });
    </script>
@endsection