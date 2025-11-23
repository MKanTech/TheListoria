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
    // KESINLIKLE DIKKAT EDIN: Bu fonksiyonun tamamını değiştirin.
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'list_type' => 'required|string|max:50',
        'is_public' => 'sometimes|accepted',
    ]);

    // Formdan is_public gelmezse (checkbox işaretlenmediyse) false olarak ata.
    $validated['is_public'] = $request->has('is_public'); 

    // $validated dizisindeki değerler:
    // is_public=true (işaretliyse) veya is_public=false (işaretli değilse) olur.

    $request->user()->lists()->create($validated);

    return redirect(route('lists.index'))->with('success', 'Liste başarıyla oluşturuldu!');
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
            'release_year' => 'nullable|integer|min:1900|max:2100',
        ]);

        // Yeni öğeyi oluştur ve listeye bağla
        $list->items()->create([
            'content' => $request->content,
            'release_year' => $request->release_year,
            // is_completed ve sort_order varsayılan olarak 0/NULL kalir
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
    
    /**
     * Liste öğesini günceller.
    */
    public function updateItem(Request $request, PList $list, ListItem $item): RedirectResponse
    {
    // GÜVENLİK KONTROLÜ: Sadece listenin sahibi güncelleyebilir ve öğenin listeye ait olduğundan emin ol
    if ($list->user_id !== Auth::id() || $item->list_id !== $list->id) {
        abort(403);
    }

    // DOĞRULAMA (Validation)
    $validated = $request->validate([
        'content' => 'required|string|max:500', 
        'release_year' => 'nullable|integer|min:1900|max:2100', // Yeni yıl kuralı
    ]);

    // Veritabanında güncelleme yap
    $item->update($validated);

    // Liste detay sayfasına başarı mesajıyla geri dön
    return redirect()->route('lists.show', $list)->with('success', 'Liste öğesi başarıyla güncellendi!');
    }
    
    
    /**
     * Listeyi siler.
     */
    public function destroy(PList $list): RedirectResponse
    {
        if ($list->user_id !== Auth::id()) {
            abort(403);
        }

        // list_items tablosunda CASCADE ayarını yaptığımız için
        // listeyi sildiğimizde, ona ait tüm öğeler de otomatik silinecektir.
        $list->delete();

        return redirect()->route('lists.index')->with('success', 'Liste başarıyla silindi!');
    }

    /**
     * Liste öğesini siler.
     */
    public function destroyItem(PList $list, ListItem $item): RedirectResponse
    {
        if ($list->user_id !== Auth::id() || $item->list_id !== $list->id) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('lists.show', $list)->with('success', 'Öğe başarıyla listeden silindi!');
    }
    /**
    * Liste düzenleme formunu gösterir.
    */
    public function edit(PList $list): View
    {
    // GUVENLIK KONTROLU: Sadece listenin sahibi düzenleyebilir
    if ($list->user_id !== Auth::id()) {
        abort(403);
    }

    return view('lists.edit', compact('list'));
    }

    /**
    * Düzenlenen liste verilerini günceller.
    */
    public function update(Request $request, PList $list): RedirectResponse
    {
    // GUVENLIK KONTROLU: Sadece listenin sahibi güncelleyebilir
    if ($list->user_id !== Auth::id()) {
        abort(403);
    }

    // DOĞRULAMA (Validation) - Yeni liste oluşturma ile aynı kurallar
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'list_type' => 'required|string|max:50',
        'is_public' => 'sometimes|accepted',
    ]);

    // is_public için varsayılan atama (checkbox işaretlenmediyse false yap)
    $validated['is_public'] = $request->has('is_public');

    // Veritabanında güncelleme yap
    $list->update($validated);

    // Liste detay sayfasına başarı mesajıyla geri dön
    return redirect()->route('lists.show', $list)->with('success', 'Liste başarıyla güncellendi!');
    }
}