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
        'pickupPointId'
    ];
    public function orderLine()
    {
        return $this->hasMany(VpOrderLine::class, 'order_id');
    }

    public function refund()
    {
        return $this->hasMany(VpOrderRefund::class);
    }

    public function shippingInfo()
    {
        return $this->hasOne(VpOrderShippingInfo::class);
    }

    public function deliveryDetail()
    {
        return $this->hasMany(VpOrderDeliveryDetail::class);
    }

    public static function getOrders()
    {  
        return VpOrder::all();
    }
}
