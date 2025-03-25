<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    protected $table = 'tbl_otp';
    protected $fillable = [
        'id ',
        'otp',
        'reciver_type',
        'reciver',
        'otp_type',
        'created_at',
        'updated_at',
    ];
}