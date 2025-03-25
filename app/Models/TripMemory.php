<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripMemory extends Model
{
    use HasFactory;
    protected $table = 'tbl_trip_memory';
    //public $timestamps = false;
     protected $fillable = [
        'id',
        'trip_id',
        'created_by',
        'activity_name',
        'caption',
        'location',
        'image',
        'created_at',
        'updated_at',
    ];

    public function getActivityName()
    {
        return $this->hasOne(TripActivity::class, 'id', 'activity_name')->select('id','name');
    }

}
