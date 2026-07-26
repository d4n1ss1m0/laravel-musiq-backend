<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class MediatekaItem extends Model
{
    protected $table = 'user_library_items';
    protected $fillable = [
        'user_id',
        'libraryable_type',
        'libraryable_id',
        'pinned_at',
        'pin_position',
    ];

    protected $casts = [
        'pinned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libraryable()
    {
        return $this->morphTo();
    }

    public function isPinned(): bool
    {
        return $this->pinned_at !== null;
    }

}
