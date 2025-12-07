<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PList;
use App\Models\ListItem;

class ItemController extends Controller
{
    // Formu Gösterir
    public function create()
    {
        $userLists = Auth::user()->lists()->get();

        return view('items.create', compact('userLists'));
    }

    // Formdan gelen veriyi işler ve listeye ekler
    public function store(Request $request)
    {
        // 1. Doğrulama (Validation)
        $validated = $request->validate([
        'content' => ['required', 'string', 'max:255'],
        'release_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 5)],
        'list_id' => ['required_without:new_list_title', 'nullable', 'exists:lists,id'], 
        'new_list_title' => ['required_without:list_id', 'nullable', 'string', 'max:100'],
        'new_list_type' => ['nullable', 'string', 'in:Genel,Film,Dizi,Kitap'],
        'new_list_description' => ['nullable', 'string', 'max:500'],
        'new_list_public' => ['nullable', 'boolean'],
        ]);

        $list = null;

        // A. Yeni Liste Oluşturma Kontrolü
        if (!empty($validated['new_list_title'])) {
        // NOT: Formda liste türü seçmediğimiz için varsayılanı 'Genel' olarak belirliyoruz.
        // Bu kısım form revizyonundan sonra değişebilir.
        $list = Auth::user()->lists()->create([
            'title' => $validated['new_list_title'],
            'description' => $validated['new_list_description'] ?? null,
            'list_type' => $validated['new_list_type'] ?? 'Genel', // Tür varsa al
            'is_public' => $request->has('new_list_public'), // Checkbox kontrolü
            'is_fixed' => false,
        ]);

        // B. Mevcut Liste Kontrolü
        }
        
        elseif (!empty($validated['list_id'])) {
        // Kullanıcının gerçekten bu listeye sahip olduğunu kontrol et
        $list = Auth::user()->lists()->findOrFail($validated['list_id']);
        }

        if (!$list) {
         return back()->withInput()->withErrors(['list_error' => 'Geçerli bir liste seçmeli veya yeni liste oluşturmalısınız.']);
        }

        // 3. Yeni Ögeyi Oluşturma
        // TÜR (type) alanını tamamen kaldırdık.
        $item = $list->items()->create([
        'user_id' => Auth::id(),
        'content' => $validated['content'],
        'release_year' => $validated['release_year'],
        'is_completed' => false, 
        ]);

        // 4. Başarı ile Yönlendirme
        return redirect()->route('lists.show', $list)
                     ->with('success', 'İçerik başarıyla "' . $list->title . '" listesine eklendi!');
    }
}