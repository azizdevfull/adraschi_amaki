<?php

namespace App\Models;

use App\Enums\PaymeState;
use App\Payment\Format;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymeTransaction extends Model
{
    use HasFactory;
    const TIMEOUT = 43200000;

    const STATE_CREATED                  = 1;
    const STATE_COMPLETED                = 2;
    const STATE_CANCELLED                = -1;
    const STATE_CANCELLED_AFTER_COMPLETE = -2;

    const REASON_RECEIVERS_NOT_FOUND         = 1;
    const REASON_PROCESSING_EXECUTION_FAILED = 2;
    const REASON_EXECUTION_FAILED            = 3;
    const REASON_CANCELLED_BY_TIMEOUT        = 4;
    const REASON_FUND_RETURNED               = 5;
    const REASON_UNKNOWN                     = 10;
    protected $guarded = [];
    protected $fillable = ['state', 'create_time', 'perform_time', 'cancel_time', 'reason', 'transaction', 'code', 'order_id', 'owner_id', 'amount', 'payme_time'];
    protected $casts = [
        'state' => PaymeState::class,
        'create_time' => 'integer',
        'perform_time' => 'integer',
        'cancel_time' => 'integer',
        'reason' => 'integer',
    ];

    public function isExpired()
    {
        return $this->state == self::STATE_CREATED && abs(Format::datetime2timestamp($this->create_time) - Format::timestamp(true)) > self::TIMEOUT;
    }
}
