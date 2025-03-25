<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $table = 'tbl_like_dislike';

    protected $fillable = [
        'id',
        'activity_id',
        'user_id',
        'like_or_dislike',
        'created_at',
        'updated_at',
    ];

    public function post()
    {
        return $this->belongsTo(TripActivity::class, 'activity_id', 'id');
    }
}