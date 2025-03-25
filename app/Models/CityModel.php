<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_city';
    public $timestamps = false;
    
    

    protected $fillable = [
        'id',
        'city_name',
        'state',
        'state_abbr',
        'time_zone',
        'country_name',
        'is_deleted',
        'is_default',
        'created_at',
    ];

    protected $casts = [
    ];
}
