<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Service;

use App\Models\VpStock;
use App\Models\VpPrice;

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
        $data = VpPrice::all();
        $file="price_list/prices.json";
        file_put_contents($file, $data);
        return $this->service->postFileHttp('/price-list/755', $file, 'priceList');
    }


    public function uploadStock(Request $request)
    {
        $data = VpStock::all();
        $file="stock_list/stock.json";
        file_put_contents($file, $data);
        return $this->service->postFileHttp('/stock', $file, 'stock');
    }
}
