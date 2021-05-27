<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Service;

class OrderController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service = new Service();
    }

    public function index()
    {
        return $this->service->getHttp('/orders');
    }

    public function show( $order )
    {
        return $this->service->getHttp('/orders/'.$order);
    }
}
