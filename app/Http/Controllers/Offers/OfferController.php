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
}
