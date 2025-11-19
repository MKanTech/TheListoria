<?php

namespace App\Http\Controllers;

use App\Models\ListItem;
use App\Models\PList;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PListController extends Controller
{
    /**
     * Tüm Listeleri Gösterme (Kullanıcı giriş yapmışsa sadece kendi listelerini gösteririz)
     */
    public function index(): View
    {
        // Sadece giriş yapmış kullanıcının listelerini çek
        $lists = Auth::user()->lists()->latest()->get();

        return view('lists.index', [
            'lists' => $lists,
        ]);
    }

    /**
     * Yeni Liste Oluşturma Formunu Gösterme
     */
    public function create(): View
    {
        return view('lists.create');
    }

    /**
     * Yeni Listeyi Veritabanına Kaydetme
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Auth::user()->lists()->create([
            'title' => $request->title,
            'description' => $request->description,
            'is_public' => $request->has('is_public'), // Checkbox işaretliyse 1, değilse 0 olur
        ]);

        return redirect()->route('lists.index')->with('success', 'Liste başarıyla oluşturuldu!');
    }
    /**
     * Belirli bir listenin detaylarını ve öğelerini gösterir.
     */
    public function show(PList $list): View
    {
        // Kullanıcının sadece kendi listesini görmesini sağlayan güvenlik kontrolü
        if ($list->user_id !== Auth::id()) {
            // Eğer liste kullanıcıya ait değilse, 403 (Yetkisiz) hatası döndür.
            abort(403); 
        }

        return view('lists.show', [
            'list' => $list,
            'items' => $list->items()->orderBy('sort_order')->get(),
        ]);
    }
    
    /**
     * Belirli bir listeye yeni bir öğe ekler.
     */
    public function storeItem(Request $request, PList $list): RedirectResponse
    {
        // Güvenlik: Kullanıcının sadece kendi listesine öğe eklemesini sağlayan kontrol
        if ($list->user_id !== Auth::id()) {
            abort(403);
        }

        // Gelen verileri kontrol etme
        $request->validate([
            'content' => 'required|string|max:500', // Öğenin içeriği
        ]);

        // Yeni öğeyi oluştur ve listeye bağla
        $list->items()->create([
            'content' => $request->content,
        ]);

        // Detay sayfasına geri yönlendir ve mesaj göster
        return redirect()->route('lists.show', $list)->with('success', 'Öğe başarıyla listeye eklendi!');
    }
    
    /**
     * Belirli bir öğenin tamamlama durumunu tersine çevirir.
     */
    public function toggleItem(PList $list, ListItem $item): RedirectResponse
    {
        // Güvenlik: Öğe, kullanıcının listesine ait mi?
        if ($list->user_id !== Auth::id() || $item->list_id !== $list->id) {
            abort(403);
        }

        // is_completed alanının değerini tersine çevirir (1 ise 0, 0 ise 1 yapar)
        $item->is_completed = ! $item->is_completed;
        $item->save();

        return redirect()->route('lists.show', $list);
    }

}