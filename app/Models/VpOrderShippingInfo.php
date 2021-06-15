<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpOrderShippingInfo extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        "name",
        "company",
        "address",
        "city",
        "state",
        "zipCode",
        "country",
        "countryIsoCode",
        "email",
        "phone"
    ];
    
    public function order()
    {
        return $this->belongsTo(VpOrder::class);
    }
}
