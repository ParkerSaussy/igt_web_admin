<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;
    protected $table = 'tbl_inquiry';

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'message',
        'is_replied',
        'created_at',
        'updated_at',
    ];
}
