<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class PasswordresettokenModel extends Model
{
    use HasFactory;

    protected $table = 'passwordresettoken';
    protected $primaryKey = 'PasswordresetTokenId';
    public $timestamps = false;

    protected $fillable = [
     'UserId','Token','IsExpired','IsActive','IsDelete','CreatedAt','UpdatedAt'
    ];
}
