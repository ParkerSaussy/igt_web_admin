<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_faq';
    protected $fillable = [
        'id',
        'question',
        'answer',
        'is_active',
        'created_at',
        'updated_at',
        
    ];
}
