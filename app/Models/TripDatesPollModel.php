<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDatesPollModel extends Model
{
    use HasFactory;
    protected $table = 'trip_dates_poll';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'trip_dates_list_id',
        'guest_id',
        'is_selected',
        'is_deleted',
        'created_at',
    ];

    protected $casts = [
        'is_selected' => 'boolean',
        'is_deleted' => 'boolean',
    ];
    public function guestDetails()
    {
        return $this->hasOne(GuestListModel::class, 'id', 'guest_id');
    }
    
}
