<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpenseTransferModel extends Model
{
    use HasFactory;
    protected $table = 'tbl_expense_transfer';
    public $timestamps = true;
    protected $fillable = [
        'id',
        'trip_id',
        'paid_by',
        'sender',
        'receiver',
        'amount'
    ];

}
