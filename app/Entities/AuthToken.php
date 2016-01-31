<?php

namespace Dietando\Entities;

use Illuminate\Database\Eloquent\Model;

class AuthToken extends Model
{
    protected $table = "auth_tokens";

    protected $fillable = [
        'user_id',
        'token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
