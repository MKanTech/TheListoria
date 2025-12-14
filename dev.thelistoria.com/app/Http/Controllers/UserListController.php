<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserList;
use Illuminate\Validation\Rule;

class UserListController extends Controller
{
    const LIST_TYPES = ['Genel', 'Film', 'Kitap', 'Dizi', 'Oyun', 'Müzik', 'Yemek', 'Diğer'];
    /**
     * Sadece giriş yapmış kullanıcıların erişimine izin verir.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Yeni liste oluşturma formunu gösterir.
     */
    public function create()
    {
        $types = self::LIST_TYPES;
        return view('lists.create', compact('types'));
    }

    /**
     * Yeni listeyi veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        $types = self::LIST_TYPES;
        
        $request->validate([
            'name' => [
            'required', 
            'string', 
            'max:255',
                // YENİ HALİ: Sadece bu kullanıcıya ait listeler içinde benzersiz olmalı
                Rule::unique('lists')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                
                }),
            ],
            'description' => 'nullable|string|max:500',
            'is_public' => 'required|boolean',
            'type' => ['required', 'string', Rule::in($types)],
        ]);

        UserList::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'is_public' => $request->is_public,
            'is_fixed' => 0,
        ]);

        // Listeler görüntüsüne yönlendir (Şimdilik profil sayfası)
        return redirect()->route('profile.show', ['username' => Auth::user()->username])
                         ->with('success', 'Yeni liste başarıyla oluşturuldu.');
    }

    /**
     * Listeyi düzenleme formunu gösterir.
     */
    public function edit(UserList $list)
    {
        // Yetki Kontrolü: Liste sahibinin kendisi olmalı ve sabit olmamalı
        if ($list->user_id !== Auth::id() || $list->is_fixed) {
            abort(403, 'Bu listeyi düzenleme yetkiniz yok.');
        }

        $types = ['Genel', 'Film', 'Kitap', 'Dizi', 'Oyun', 'Müzik', 'Yemek', 'Diğer'];
        return view('lists.edit', compact('list', 'types'));
    }

    /**
     * Listeyi günceller.
     */
    public function update(Request $request, UserList $list)
    {
        // Yetki Kontrolü
        if ($list->user_id !== Auth::id() || $list->is_fixed) {
            abort(403, 'Bu listeyi düzenleme yetkiniz yok.');
        }

        if ($list->user_id !== Auth::id() || $list->is_fixed) {
            abort(403, 'Bu listeyi düzenleme yetkiniz yok veya sabit bir listedir.');
        }
        
        $types = ['Genel', 'Film', 'Kitap', 'Dizi', 'Oyun', 'Müzik', 'Yemek', 'Diğer'];

        $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255',
                // Kullanıcının kendi listeleri içinde benzersiz olmalı (ancak bu listeyi hariç tut)
                Rule::unique('lists')->where(function ($query) use ($list) {
                    return $query->where('user_id', Auth::id())
                                 ->where('id', '!=', $list->id); // Kendisini kontrol dışında tut
                }),
            ],
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
            'type' => ['required', 'string', Rule::in($types)],
        ]);

        $list->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'is_public' => $request->has('is_public'),
        ]);

        return redirect()->route('lists.show', $list->id)
                         ->with('success', 'Liste başarıyla güncellendi.');
    }

    /**
     * Listeyi siler.
     */
    public function destroy(UserList $list)
    {
        // Yetki Kontrolü
        if ($list->user_id !== Auth::id() || $list->is_fixed) {
            abort(403, 'Bu listeyi silme yetkiniz yok.');
        }

        // Listeye bağlı içerikleri (list_post pivot) kaldırır
        $list->posts()->detach(); 
        
        // Listeyi siler
        $list->delete();

        return redirect()->route('profile.show', ['username' => Auth::user()->username])
                         ->with('success', $list->name . ' listesi başarıyla silindi.');
    }
    
    /**
     * Bir listenin detayını ve içerdiği tüm postları gösterir.
     */
    public function show(UserList $list)
    {
        $user = Auth::user();
        
        // Yetki Kontrolü:
        // 1. Liste gizliyse (is_public = 0) ve kullanıcı liste sahibi değilse, erişimi engelle.
        if (!$list->is_public && $list->user_id !== $user->id) {
            abort(403, 'Bu gizli listeyi görüntüleme yetkiniz yok.');
        }

        // Listeye bağlı içerikleri (Postları) çekiyoruz.
        // Pivot verisine (user_status) erişmek için withPivot kullanıyoruz.
        $posts = $list->posts()
                      ->withPivot('user_status') // user_status'u çekmeyi unutmayalım
                      ->orderBy('list_post.created_at', 'desc')
                      ->paginate(20); // Sayfalama ekleyelim
        
        // Liste sahibinin biz olup olmadığımızı kontrol et
        $is_owner = $list->user_id === $user->id;

        return view('lists.show', compact('list', 'posts', 'is_owner'));
    }
    
    
    
    /**
     * Bir postu manuel bir listeden çıkarır (detach).
     * @param UserList $list
     * @param Post $post_id: Listeden çıkarılacak Post'un ID'si
     */
    
    public function removePost(UserList $list, $postId)
    {
        // Yetki Kontrolü: İşlemi sadece liste sahibi yapabilir.
        if ($list->user_id !== Auth::id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Sabit listelerden içerik çıkarılamaz. (Çıkarma işi PostController@update yapacak)
        if ($list->is_fixed) {
            return back()->with('error', 'Sabit listelerden içerik bu yolla çıkarılamaz. Durumu güncellemelisiniz.');
        }

        // Pivot kaydını sil (detach)
        $list->posts()->detach($postId);

        return back()->with('success', 'İçerik, ' . $list->name . ' listesinden başarıyla çıkarıldı.');
    }
}