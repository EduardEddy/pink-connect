<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpOrderDeliveryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        "carrierId",
        "carrierName",
        "trackingNumber",
        "trackingUrl"
    ];

    public function order()
    {
        return $this->belongsTo(VpOrder::class);
    }
}
