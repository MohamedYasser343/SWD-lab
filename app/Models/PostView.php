<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['post_id', 'user_id', 'session_id', 'day_key', 'ip_hash', 'viewed_at'])]
class PostView extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
        ];
    }
}
