<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitiesListModel extends Model
{
    use HasFactory;
    protected $table = 'trip_city_list';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'trip_id',
        'city_id',
        'is_deleted',
        'created_at',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'city_id' => 'int',
    ];

    public function tripData()
    {
        return $this->belongsTo(TripDetails::class, 'trip_id', 'id');
    }
    public function tripCityPolls()
    {
        return $this->hasMany(TripCityPollModel::class, 'trip_city_list_id', 'id');
    }
    public function cityNameDetails()
    {
        return $this->hasOne(CityModel::class, 'id', 'city_id');
    }
}

