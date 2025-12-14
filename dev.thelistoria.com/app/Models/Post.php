<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'cover_image_url',
        'publish_year',
    ];

    // Bir gönderi bir kullanıcıya aittir.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Bir gönderi birden çok listede yer alabilir (Çoktan çoğa ilişki).
    public function lists()
    {
        return $this->belongsToMany(UserList::class, 'list_post', 'post_id', 'list_id')
                    ->withPivot('user_status')
                    ->withTimestamps();
    }
    
    // Gönderiye gelen Favoriler (Kalp beğenileri)
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Gönderiye gelen Parmak Onayları
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    // Gönderiye gelen yorumlar
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}