<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpOrder extends Model
{
    use HasFactory;

    
    protected $primary = 'id';
    public $incrementing = false;
    protected $fillable = [
        'marketplaceName',
        'marketplaceCode',
        'marketplaceOrderCode',
        'shopChannelId',
        'shopChannelName',
        'status',
        'shippedOrderDate',
        'totalPrice',
        'shippingCosts',
        'shippingTaxRate',
        'currency',
        'requestedShippingMethod',
        'deliveryNote',
        'pickupPointId',
        'updated'
    ];
    public function orderLine()
    {
        return $this->hasMany(VpOrderLine::class, 'order_id');
    }

    public function refund()
    {
        return $this->hasMany(VpOrderRefund::class, 'order_id');
    }

    public function shippingInfo()
    {
        return $this->hasOne(VpOrderShippingInfo::class, 'order_id');
    }

    public function deliveryDetail()
    {
        return $this->hasMany(VpOrderDeliveryDetail::class, 'order_id');
    }

    public static function getOrders()
    {  
        return VpOrder::all();
    }

    public static function getDataToUpdate()
    {
        $data = VpOrder::where('updated',false)
        ->get();
        return $data;
    }

    public function getDeliverDetailsToUpdate()
    {
        $data = $this->deliveryDetail()
        ->where('updated',false)
        ->get();
        return $data;
    }

    public function setUpdated()
    {
        $this->updated=True;
    }
}
