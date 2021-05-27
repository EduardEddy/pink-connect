<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Service;

class OfferController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service = new Service();
    }

    public function getFileStatus(Request $request)
    {
        return $this->service->getHttp('/status/'.$request->file);
    }

    Public function create()
    {
        $data = [
            [
                "gtin"=>"abc",
                "reference"=>"abc",
                "name"=>"abc",
                "brand"=>"abc",
                "base"=>"abc",
                "price"=>"abc",
                "selling"=>"abc",
                "price"=>"abc",
                "discount"=>"abc",
                "stock"=>"abc", 
                "available"=>"abc",
                "price_list_name"=>"abc",	
                "discount_start"=>"abc",
                "discount_end"=>"abc",
                "marketplace_status"=>"abc"
            ]
        ];
        return $this->service->postHttp('/price-list/755', $data);
    }
}
