<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestListModel extends Model
{
    use HasFactory;
    protected $table = 'trip_guests';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'trip_id',
        'first_name',
        'last_name',
        'email_id',
        'phone_number',
        'role',
        'is_co_host',
        'is_deleted',
        'no_of_invite_send',
        'invite_status',
        'u_id',
        'last_invitation_time',
        'created_at',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'is_co_host' => 'boolean',
    ];

    public function guestTrip()
    {
        return $this->belongsTo(TripDetails::class, 'trip_id', 'id');
    }

    public function usersTrips()
    {
        return $this->hasMany(TripDetails::class, 'id', 'trip_id');
    }

    //To get user data
    public function usersDetailG()
    {
        return $this->belongsTo(User::class, 'id', 'u_id');
    }

    public function usersDetailProfileImage()
    {
        return $this->hasOne(User::class, 'id', 'u_id')->select(['id','profile_image']);
    }

}
