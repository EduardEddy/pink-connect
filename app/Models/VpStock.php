<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VpStock extends Model
{
    use HasFactory;
    protected $fillable = [
        'gtin', 'sku', 'stock','updated'
    ];

    public static function getDataToUpdate()
    {
        $data = VpStock::select('gtin', 'sku', 'stock')
        ->where('updated',false)
        ->get();
        return $data;
    }
}
