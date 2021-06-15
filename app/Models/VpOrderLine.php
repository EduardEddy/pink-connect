<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpOrderLine extends Model
{
    use HasFactory;
        
    protected $fillable = [
        'order_id',
        "gtin",
        "sku",
        "name",
        "price",
        "taxRate",
        "quantity",
        "brandName",
        "totalPrice",
        "manufacturerReference",
        "in_stock",
        "out_of_stock",
        "shipped",
        "returned",
        "cancelled",
        "processing",
        "pending"
    ];

    public function order()
    {
        return $this->belongsTo(VpOrder::class);
    }

    public function changeStatus($status){
        switch ($status) {
            case 'PENDING':
                $this->pending = $this->quantity;
                break;
            case 'SHIPPED':
                $this->shipped = $this->shipped + $this->quantity;
                $this->processing = $this->processing - $this->quantity;
                break;

            case 'PROCESSING':
                $this->processing = $this->processing + $this->quantity;
                $this->pending = $this->pending - $this->quantity;
                break;

            case 'CANCELLED':
                $this->cancelled = $this->cancelled + $this->quantity;
                $this->pending = 0;
                $this->processing = 0;
                $this->shipped = 0;
                break;
            
            default:
                # code...
                break;
        }
    }


    public static function getLine($type,$value,$order)
    {
        return VpOrderLine::select()->where('order_id','=',$order)
                                    ->where($type,'=',$value)->get();
    }
}
