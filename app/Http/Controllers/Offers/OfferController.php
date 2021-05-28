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

    public function index(Request $request)
    {
        $offset = $request->offset ? $request->offset : 0;
        $limit = $request->limit ? $request->limit : 50;
        $shopId = 755;
        $route = '/offers?shopChannelId='.$shopId.'&offset='.$offset.'&limit='.$limit;
        return $this->service->getHttp($route);
    }

    public function getFileStatus(Request $request)
    {
        return $this->service->getHttp('/status/'.$request->file);
    }

    Public function priceList()
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


    public function uploadStock(Request $request)
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
        return $this->service->postHttp('/stock', $data);
    }
}
