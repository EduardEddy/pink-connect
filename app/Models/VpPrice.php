<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpPrice extends Model
{
    use HasFactory;
    protected $fillable = [
        'gtin', 'sku', 'selling_price', 'manufacturer_recommended_price', 'updated'
    ];

    /**QUERYs */
    public static function getDataToUpdate()
    {
        $price_list = VpPrice::select('gtin', 'sku', 'selling_price', 'manufacturer_recommended_price')
        ->where('updated',false)
        ->get();
        return $price_list;
    }
}
