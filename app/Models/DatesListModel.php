<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatesListModel extends Model
{
    use HasFactory;
    protected $table = 'trip_dates_list';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'trip_id',
        'start_date',
        'end_date',
        'comment',
        'is_deleted',
        'is_default',
        'created_at',
        'vipVoted'
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function tripDatePolls()
    {
        return $this->hasMany(TripDatesPollModel::class, 'trip_dates_list_id', 'id');
    }
}
