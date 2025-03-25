<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_expense';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'trip_id',
        'guest_id',
        'amount',
        'name',
        'description',
        'expense_on',
        'created_by',
        'created_on',
        'type',
        'deposit_amount'
    ];

    public function shares(): HasMany
    {
        return $this->hasMany(ExpenseShareModel::class,'expense_id','id');
    }
}
