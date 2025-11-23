<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListItem extends Model
{
    use HasFactory;

    // Modelin kullanacağı veritabanı tablosu
    protected $table = 'list_items';

    // Toplu atama sırasında doldurulmasına izin verilen alanlar
    protected $fillable = [
        'list_id',
        'content',
        'item_type',
        'release_year',
        'is_completed',
        'sort_order',
    ];

    /**
     * Bu öğenin ait olduğu listeyi tanımlar.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(PList::class, 'list_id');
    }
}