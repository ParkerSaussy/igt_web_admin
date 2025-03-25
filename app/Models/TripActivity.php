<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripActivity extends Model
{
    use HasFactory;
    protected $table = 'tbl_trip_activities';
    protected $fillable = [
        'id',
        'user_id',
        'trip_id',
        'activity_type',
        'name',
        'event_date',
        'event_time',
        'departure_date',
        'checkout_time',
        'discription',
        'url',
        'address',
        'cost',
        'spent_hours',
        'number_of_nights',
        'average_nightly_cost',
        'capacity_per_room',
        'room_number',
        'arrival_flight_number',
        'departure_flight_number',
        'departure_flight_number',
        'is_itineary',
        'created_at',
        'updated_at',
        'notification_sent',
        'utc_time'
    ];

    public function likes()
    {
        return $this->hasMany(Like::class, 'activity_id', 'id');
    }

    public function guests()
    {
        return $this->hasMany(GuestListModel::class, 'trip_id', 'trip_id')->where('is_deleted',false);
    }
}