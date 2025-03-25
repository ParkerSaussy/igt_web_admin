<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripDocument extends Model
{
    use HasFactory;
    protected $table = 'tbl_trip_document';
    //public $timestamps = false;
     protected $fillable = [
        'id',
        'trip_id',
        'document_name',
        'document',
        'size',
        'uploaded_by',
        'is_deleted',
        'created_at',
        'updated_at',
    ];
}
