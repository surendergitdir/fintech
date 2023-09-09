<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $fillable = [
        'initiated_by',
        'txn_id',
        'receiver_name',
        'receiver_account_no',
        'amount',
        'currency',
        'sender_name',
        'sender_account_no',
        'reference',
        'transaction_type',
        'status'
    ];
    protected $guarded = ['id'];
}
