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
        "trackingUrl",
        "updated"
    ];

    public function order()
    {
        return $this->belongsTo(VpOrder::class);
    }

    public function setUpdated()
    {
        $this->updated=True;
        $this->save();
    }

    // public static function getDataToUpdate()
    // {
    //     $data = VpOrderDeliveryDetail::select('order_id', 'carrierId', 'carrierName', 'trackingNumber', 'trackingUrl')
    //     ->where('updated',false)
    //     ->get();
    //     return $data;
    // }
}
