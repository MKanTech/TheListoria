<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\UserList;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    // Listelerde kullanılabilecek türler
    const LIST_TYPES = ['Genel', 'Film', 'Kitap', 'Dizi', 'Oyun', 'Müzik', 'Yemek', 'Diğer'];
    
    // Sadece giriş yapmış kullanıcıların erişimine izin verir.
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Kullanıcının tüm postlarını listeler ve arama yapar.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $postsQuery = Auth::user()->posts()->orderBy('created_at', 'desc');

        if ($search) {
            $postsQuery->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        $posts = $postsQuery->paginate(15);
        
        return view('posts.index', compact('posts', 'search'));
    }

    /**
     * Yeni post ekleme formunu gösterir.
     */
    public function create()
    {
        // Kullanıcının tüm manuel listelerini çek (is_fixed olmayanlar)
        $manualLists = Auth::user()->lists()->where('is_fixed', 0)->get();
        $types = self::LIST_TYPES;
        
        return view('posts.create', compact('manualLists', 'types'));
    }

    /**
     * Yeni postu (içeriği) veritabanına kaydeder.
     */
    public function store(Request $request)
    {
        // 1. POST Validasyonu
        $validationRules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publish_year' => 'nullable|integer|digits:4',
            'user_status' => 'required|in:Beklemede,Devam Ediyor,Tamamlandı', // user_status kullanılıyor
            'list_ids' => 'nullable|array',
            'list_ids.*' => 'exists:lists,id',
            'is_new_list' => 'required|boolean', // Eklenen gizli flag
        ];

        $types = self::LIST_TYPES; 

        if ($request->is_new_list == 1) {
            $validationRules['new_list_name'] = [
                'required', 
                'string', 
                'max:255',
                // Doğru Rule::unique kullanımı: 'lists' tablosundaki 'name' sütununu kontrol et
                Rule::unique('lists', 'name')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ];
            $validationRules['new_list_type'] = ['required', 'string', Rule::in($types)];
            $validationRules['new_list_description'] = 'nullable|string|max:500';
        }

        $request->validate($validationRules);

        // 2. POST Oluşturma
        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'publish_year' => $request->publish_year,
        ]);

        $userStatus = $request->user_status; // Doğru değişken kullanımı
        $syncListIds = [];

        // 3. LİSTE YÖNETİMİ
        if ($request->is_new_list == 1) {
            // A) YENİ LİSTE OLUŞTUR ve syncListIds'ye ekle
            $newList = UserList::create([
                'user_id' => Auth::id(),
                'name' => $request->new_list_name,
                'description' => $request->new_list_description,
                'type' => $request->new_list_type,
                'is_public' => $request->has('new_list_is_public'),
                'is_fixed' => 0,
            ]);

            $syncListIds[] = $newList->id;

        } else {
            // B) MEVCUT LİSTELERDEN EKLE
            $syncListIds = $request->list_ids ?? [];
        }

        // 4. Sabit Listeleri Güncelle ve Post'u Listelerle İlişkilendir

        // Sabit Listeler için: Post durumuna uygun listeyi bulur ve syncListIds'ye ekler.
        $fixedListId = $this->updateFixedLists($post, $userStatus);
        if ($fixedListId) {
            $syncListIds[] = $fixedListId;
        }

        // İlişkilendirme için syncData hazırla.
        $syncData = [];
        foreach (array_unique($syncListIds) as $listId) {
            $syncData[$listId] = ['user_status' => $userStatus];
        }

        // Tüm ilişkileri senkronize et
        $post->lists()->sync($syncData); 

        return redirect()->route('posts.show', $post->id)
                         ->with('success', 'İçerik başarıyla eklendi ve listelendi.');
    }
    
    /**
     * Post'un detay sayfasını gösterir.
     */
    public function show(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Bu içeriği görme yetkiniz yok.');
        }

        $attachedLists = $post->lists()->get(); 

        return view('posts.show', compact('post', 'attachedLists'));
    }
    
    /**
     * İçeriği veritabanından kalıcı olarak siler.
     */
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Bu içeriği silme yetkiniz yok.');
        }

        $postTitle = $post->title;
        
        $post->lists()->detach(); 
        $post->delete();

        return redirect()->route('profile.show', ['username' => Auth::user()->username])
                         ->with('success', $postTitle . ' içeriği ve tüm liste bağlantıları kalıcı olarak silindi.');
    }
    
    /**
     * İçerik düzenleme formunu gösterir.
     */
    public function edit(Post $post)
    {
        // Yetki Kontrolü: Sadece içeriğin sahibi düzenleyebilir.
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Bu içeriği düzenleme yetkiniz yok.');
        }

        // Kullanıcının sadece MANUEL listelerini çek (is_fixed=0 olmayanlar).
        // View'e gönderilecek değişken adı artık $manualLists olacak.
        $manualLists = Auth::user()->lists()->where('is_fixed', 0)->get();

        // İçeriğin hangi listelerde olduğunu gösteren bir dizi oluştur
        // Bu, View'deki checkbox'ların işaretli gelmesini sağlar.
        $postListIds = $post->lists->pluck('id')->toArray();
        
        // Liste türlerini gönder
        $types = self::LIST_TYPES;

        // Post'un geçerli durumunu çek (Devam Ediyor/Tamamlandı/Beklemede)
        // Eğer post hiçbir sabit listeye bağlı değilse (Beklemede), varsayılan olarak 'Beklemede' döner.
        $currentStatus = $post->lists()->wherePivotIn('user_status', ['Beklemede', 'Devam Ediyor', 'Tamamlandı'])
                                       ->wherePivot('post_id', $post->id) // Post'a özgü user_status'u çekmek için.
                                       ->first()->pivot->user_status ?? 'Beklemede';

        // View'e gönderilen değişkenleri kontrol et: $manualLists, $postListIds, $types, $currentStatus
        return view('posts.edit', compact('post', 'manualLists', 'postListIds', 'types', 'currentStatus'));
    }
    
    /**
     * İçeriği ve liste ilişkilerini günceller.
     */
    public function update(Request $request, Post $post)
    {
        // Yetki Kontrolü: Sadece içeriğin sahibi güncelleyebilir.
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Bu içeriği güncelleme yetkiniz yok.');
        }

        // --- 1. VALIDASYON KURALLARI ---
        $validationRules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'publish_year' => 'nullable|integer|digits:4',
            'user_status' => 'required|in:Beklemede,Devam Ediyor,Tamamlandı', // user_status kullanılıyor
            'list_ids' => 'nullable|array',
            'list_ids.*' => 'exists:lists,id',
            
            // Yeni Liste Oluşturma Bayrağı (Edit sayfasından gelir)
            'should_create_new_list' => 'nullable|boolean',
        ];

        $types = self::LIST_TYPES;

        // Eğer yeni liste oluşturulacaksa validasyon kurallarını ekle
        if ($request->should_create_new_list == 1) {
            $validationRules['new_list_name'] = [
                'required', 
                'string', 
                'max:255',
                // Kullanıcının listeleri içinde benzersizlik kontrolü (Kullanıcı listeleri ve mevcut postun bağlı olduğu listeler hariç)
                Rule::unique('lists', 'name')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ];
            $validationRules['new_list_type'] = ['required', 'string', Rule::in($types)];
            $validationRules['new_list_description'] = 'nullable|string|max:500';
            $validationRules['new_list_is_public'] = 'nullable|boolean'; // Checkbox değeri
        }

        $request->validate($validationRules);
        
        // --- 2. POST ANA BİLGİLERİNİ GÜNCELLE ---
        $post->update([
            'title' => $request->title,
            'description' => $request->description,
            'publish_year' => $request->publish_year,
        ]);
        
        $userStatus = $request->user_status;
        $syncListIds = []; // Senkronize edilecek tüm listeleri tutar
        
        // --- 3. YENİ LİSTE YÖNETİMİ (İsteğe Bağlı) ---
        if ($request->should_create_new_list == 1) {
             $newList = UserList::create([
                'user_id' => Auth::id(),
                'name' => $request->new_list_name,
                'description' => $request->new_list_description,
                'type' => $request->new_list_type,
                'is_public' => $request->has('new_list_is_public'), // Checkbox kontrolü
                'is_fixed' => 0,
            ]);
            $syncListIds[] = $newList->id; // Yeni listeyi senkronize listelerine ekle
        }

        // --- 4. MANUEL LİSTELERİ VE SABİT LİSTELERİ BİRLEŞTİRME ---
        
        // A. Formdan gelen seçili manuel listeleri (list_ids) ekle
        $selectedManualListIds = $request->list_ids ?? [];
        $syncListIds = array_merge($syncListIds, $selectedManualListIds);
        
        // B. Sabit Listeleri Güncelle (Devam Edenler/Tamamlananlar listelerini yönetir)
        // Bu metod postu eski durum listesinden çıkarıp yenisine ekler (sadece Devam/Tamam).
        $fixedListIdForStatus = $this->updateFixedLists($post, $userStatus); 

        // C. Post'un şu anda bağlı olduğu TÜM (Devam/Tamam/Fav/Like) sabit listeleri koruma altına al
        $fixedLists = Auth::user()->lists()->where('is_fixed', 1)->get(); 
        $allFixedListIds = $fixedLists->pluck('id')->toArray();
        
        // Post'un hali hazırda (güncelleme öncesi) bağlı olduğu ve sabit olan listeler
        // Bu, Fav/Like gibi diğer sabit listelerin kaybolmamasını sağlar.
        foreach ($allFixedListIds as $fixedId) {
             if ($post->lists->contains('id', $fixedId)) {
                 $syncListIds[] = $fixedId;
             }
        }
        
        // Not: updateFixedLists'in eklediği $fixedListIdForStatus, syncListIds içinde zaten olmalı (ya updateFixedLists metodu içindeki attach ya da post->lists->contains kontrolü ile).

        // --- 5. SENKRONİZASYON ---
        
        // Sync Data'yı oluştur (aynı listeye iki kez eklenmesini önler)
        $syncData = [];
        foreach (array_unique($syncListIds) as $listId) {
            // Tüm listelere yeni user_status'u pivot verisi olarak ekle
            $syncData[$listId] = ['user_status' => $userStatus]; 
        }

        // Senkronizasyonu yap. sync() metodunda $syncData'da olmayan tüm eski ilişkiler silinir.
        $post->lists()->sync($syncData);

        return redirect()->route('posts.show', $post->id) 
                         ->with('success', 'İçerik başarıyla güncellendi.');
    }
    
    /**
     * Kullanıcının user_status'una göre sabit listeleri günceller.
     * Bu metod, Devam Edenler ve Tamamlananlar listelerinin yönetimini yapar.
     */
    private function updateFixedLists(Post $post, $userStatus)
    {
        $user = Auth::user();
        
        // Tüm sabit listeleri çekip isimlerine göre indexleyelim.
        $fixedLists = $user->lists()
                            ->where('is_fixed', 1)
                            ->get()
                            ->keyBy('name');
        
        $devamEdenlerId = $fixedLists['Devam Edenler']->id ?? null;
        $tamamlananlarId = $fixedLists['Tamamlananlar']->id ?? null;

        // Önce Post'u Devam Edenler ve Tamamlananlar'dan kaldır
        if ($devamEdenlerId) {
            $post->lists()->detach($devamEdenlerId);
        }
        if ($tamamlananlarId) {
            $post->lists()->detach($tamamlananlarId);
        }

        // Yeni duruma göre tekrar ekle
        if ($userStatus == 'Devam Ediyor' && $devamEdenlerId) {
            $post->lists()->attach($devamEdenlerId, ['user_status' => $userStatus]);
            return $devamEdenlerId; // Eklenen listeyi döndür
        } elseif ($userStatus == 'Tamamlandı' && $tamamlananlarId) {
            $post->lists()->attach($tamamlananlarId, ['user_status' => $userStatus]);
            return $tamamlananlarId; // Eklenen listeyi döndür
        }
        
        return null; // Hiçbir sabit listeye eklenmediyse null döndür (Beklemede veya diğer sabit listeler)
    }
}