<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    use HasFactory;
    protected $table = 'tbl_timezone_';
    public $timestamps = false;
     protected $fillable = [
        'id',
        'value',
        'abbr',
        'offset',
        'isdst',
        'text',
        'CreatedAt',
        'UpdatedAt',
    ];
}
