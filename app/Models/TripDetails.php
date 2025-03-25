<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDetails extends Model
{
    use HasFactory;
    protected $table = 'trip_details';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'trip_name',
        'trip_description',
        'itinary_details',
        'response_deadline',
        'reminder_days',
        'trip_img_url',
        'created_by',
        'deleted_by',
        'updated_by',
        'updated_on',
        'is_trip_finalised',
        'trip_final_start_date',
        'trip_final_end_date',
        'trip_final_city',
        'trip_finalizing_comments',
        'trip_finaled_on',
        'is_deleted',
        'created_at',
        'previous_reminder_date',
        'is_paid',
        'paid_by',
        'paid_on',
        'paid_plan_type',
        'dropbox_url'
    ];

    protected $casts = [
        'is_trip_finalised' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function cityNameDetails()
    {
        return $this->hasOne(CityModel::class, 'id', 'trip_final_city')->withDefault();
    }
    public function city()
    {
        //return $this->hasMany(CitiesListModel::class, 'trip_id', 'id');
        //return $this->hasManyThrough(CityModel::class, CitiesListModel::class, 'trip_id', 'city_id', 'id', 'city_id');
        return $this->hasManyThrough(
            CityModel::class,      // The final related model (CityModel)
            CitiesListModel::class, // The intermediate model (CitiesListModel)
            'trip_id',             // Foreign key on CitiesListModel
            'id',                  // Local key on TripModel
            'id',             // Foreign key on CityModel
            'city_id'                   // Local key on CitiesListModel
        );
    }

    public function dates()
    {
        return $this->hasMany(DatesListModel::class, 'trip_id', 'id')->where('is_deleted',false);
    }

    public function guests()
    {
        return $this->hasMany(GuestListModel::class, 'trip_id', 'id')->where('is_deleted',false)->where('invite_status',"Approved");
    }

    public function guestsWithId($userId)
    {
        return $this->hasMany(GuestListModel::class, 'trip_id', 'id')
        ->where('u_id',$userId)
        ->where('is_deleted',false);
    }

    public function hostDetail()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function premiumPlanBy()
    {
        return $this->hasOne(User::class, 'id', 'paid_by')->withDefault();
    }

    public function abc()
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }

    public function tripId()
    {
        return $this->belongsTo(GuestListModel::class, 'id', 'trip_id');
    }

    public function coHosts()
{
    return $this->hasMany(GuestListModel::class, 'trip_id', 'id')->where('is_co_host',1)->where('invite_status','Approved');
   
}
    public function coHostCount()
    {
        return $this->coHosts()->count();
    }
    
    public static function getTripDetails($tripId){
    //     $data = TripDetails::join('tbl_users', 'trip_details.created_by', '=', 'tbl_users.id')
    //     ->leftJoin('trip_city_list', 'trip_details.id', '=', 'trip_city_list.trip_id')
    //     ->where('trip_details.id',$tripId)
    //     ->select('trip_details.*','trip_city_list.*','tbl_users.first_name','tbl_users.last_name' )
    //     ->get();
    //     return $data;

   
   // return $data;
     }   
}