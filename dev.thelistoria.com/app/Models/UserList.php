<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
    use HasFactory;
    
    protected $table = 'lists'; 

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'type',
        'is_public',
        'is_fixed',
    ];

    // Bir liste bir kullanıcıya aittir.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Bir listenin birden çok gönderisi vardır (Çoktan çoğa ilişki, list_post pivot tablosu üzerinden)
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'list_post', 'list_id', 'post_id')
                    // Pivot tablodaki özel alanlarımızı ekliyoruz
                    ->withPivot('user_status') 
                    ->withTimestamps();
    }
}