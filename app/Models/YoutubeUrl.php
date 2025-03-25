<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeUrl extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'tbl_setting';
    //public $timestamps = false;
     protected $fillable = [
        'id',
        'type',
        'key',
        'value',
        'created_at',
        'updated_at',
    ];
}
