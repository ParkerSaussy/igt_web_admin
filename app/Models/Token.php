<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;
    protected $table = 'tbl_auth_token';
    protected $fillable = [
        'user_id',
        'auth_token',
        'fcm_token',
        'device_id',
        'platform',
    ];

}
