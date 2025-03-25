<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseShareModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_expense_share';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'expense_id',
        'trip_id',
        'amount',
        'paid_amount',
        'creditor',
        'debtor',
        'paid_on',
        'paid_by',
    ];
}
