<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $table = 'tbl_plan';
  
    protected $fillable = [
        'id',
        'type',
        'name',
        'description',
        'price',
        'discounted_price',
        'duration',
        'image',
        'is_active',
        'apple_pay_key',
        'CreatedAt',
        'UpdatedAt',
        'is_delete'
    ];
}
