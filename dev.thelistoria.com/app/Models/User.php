<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserList;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // Kullanıcının sahip olduğu tüm listeler
    public function lists()
    {
        return $this->hasMany(UserList::class);
    }

    // Kullanıcının sahip olduğu tüm kişisel gönderiler
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // (Favorilere eklediği)
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // (Beğendiklerim)
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
    
    /**
     * Kullanıcının SADECE sabit listelerini getirir (Devam Edenler, Tamamlananlar, vb.)
     */
    public function fixedLists()
    {
        return $this->hasMany(UserList::class)->where('is_fixed', 1);
    }
}
