<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundAudit extends Model
{
    protected $table = 'refund_audits';

    protected $fillable = [
        'pesanan_id',
        'admin_id',
        'dp_amount',
        'penalty_percent',
        'penalty_amount',
        'final_refund',
        'note',
    ];
}
