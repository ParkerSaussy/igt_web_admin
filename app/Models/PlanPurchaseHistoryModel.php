<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPurchaseHistoryModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_plan_purchase_history';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'plan_id',
        'user_id',
        'purchase_on',
        'price',
        'plan_type',
        'duration',
        'trip_id',
        'transaction_id',
        'payment_through'
    ];
}
