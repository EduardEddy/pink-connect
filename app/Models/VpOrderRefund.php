<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpOrderRefund extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        "date",
        "productCost",
        "shippingCost",
        "currency",
        "type"
    ];
    
    public function order()
    {
        return $this->belongsTo(VpOrder::class);
    }
}
