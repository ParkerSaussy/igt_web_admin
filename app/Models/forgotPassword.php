<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgotPassword extends Model
{
    use HasFactory;
    protected $table = 'tbl_forgot_password_app';
    protected $fillable = [
        'auth_token',
        'user_id',
        'otp',
        'email',
        'created_at',
        'updated_at',
        'is_expired',
    ];
}