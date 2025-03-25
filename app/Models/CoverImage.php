<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoverImage extends Model
{
    use HasFactory;
    protected $table = 'tbl_cover_images';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'image_name',
        'created_at',
        'updated_at',
        'is_deleted'
    ];

    protected $casts = [
    ];
}
