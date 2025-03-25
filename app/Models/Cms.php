<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{
    use HasFactory;
    protected $table = 'tbl_cms';
    protected $fillable = [
        'id',
        'type',
        'description',
        'created_at',
        'updated_at',
    ];
}