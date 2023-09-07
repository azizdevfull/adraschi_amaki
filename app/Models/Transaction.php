<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'paycom_transaction_id',
        'paycom_time',
        'paycom_time_datetime',
        'create_time',
        'perform_time',
        'cancel_time',
        'amount',
        'state',
        'reason',
        'receivers',
        'order_id',
        'perform_time_unix',
    ];

    // protected $casts = [
    //     'paycom_time_datetime' => 'datetime',
    //     'create_time' => 'datetime',
    //     'perform_time' => 'datetime',
    //     'cancel_time' => 'datetime',
    //     'receivers' => 'json',
    // ];

    public static function getTransactionsByOrderIdAndState($orderId)
    {
        return self::where('order_id', $orderId)
            ->where('state', 1)
            ->get();
    }
}
