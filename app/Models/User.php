<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'tbl_users';
    protected $fillable = [
        'id',
        'signin_type',
        'email',
        'country_code',
        'mobile_number',
        'password',
        'first_name',
        'last_name',
        'paypal_username',
        'venmo_username',
        'social_id',
        'social_type',
        'is_email_verify',
        'is_mobile_verify',
        'is_active',
        'password',
        'profile_image',
        'plan_id',
        'plan_start_date',
        'plan_end_date',
        'get_chat_notfication',
        'get_push_notfication'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // protected $appends = [
    //     'profile_image',
    // ];

    // public function getProfileImageAttribute($value)
    // {
    //     return config('global.local_image_url'). $value;
    // }

    // public function setProfileImageAttribute($value)
    // {
    //     $this->attributes['profile_image'] = str_replace(config('global.local_image_url'), '', $value);
    // }
    // protected $appends = [
    //     'profile_image_full',
    // ];
    // public function getProfileImageFullAttribute()
    // {
    //     //return config('global.local_image_url'). $value;
    //     return config('global.local_image_url').$this->profile_image;
    // }
    public function trips()
    {
        return $this->hasMany(TripDetails::class, 'created_by', 'id');
    }

    public function hostUserProfile()
    {
        return $this->belongsTo(GuestListModel::class, 'u_id', 'id');
    }
    
    public function hostUserData()
    {
        return $this->hasOne(GuestListModel::class, 'u_id', 'id');
    }
}