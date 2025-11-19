<?php

namespace App\Models;

use App\Models\ListItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PList extends Model
{
    use HasFactory;

    // Modelin kullanacağı veritabanı tablosu
    protected $table = 'lists';

    // Toplu atama (Mass Assignment) sırasında doldurulmasına izin verilen alanlar
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_public',
    ];

    /**
     * Bu listenin ait olduğu kullanıcıyı tanımlar.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Listenin sahip olduğu tüm öğeleri tanımlar.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ListItem::class, 'list_id');
    }
}