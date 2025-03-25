<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'tbl_push_notification';
    protected $fillable = [
        'id ',
        'type',
        'sender_id',
        'reciver_id',
        'title',
        'message',
        'payload',
        'created_at',
        'updated_at',
    ];

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class, 'reciver_id') // Replace with your actual foreign key
            ->where('is_read', 0);
    }
}
