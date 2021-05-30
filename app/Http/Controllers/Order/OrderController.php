<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\VpStock;
use App\Services\Service;
use Illuminate\Http\Request;

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

    public function updateOrderStatus(Request $request)
    {
        //get the info from the request
        $order = $request->order;
        $status = $request->status;

        //body schema
        $data = $request->input();

        //excecute the puthttp() service with the params and data
        return $this->service->putHttp('/orders/'.$order.'/status/'.$status, $data);
    }

    public function cancelProducts($order)
    {
        //Get order info
        $or = $this->show($order);
        $status = $or['status']; 

        //request body
        $data = [];

        //temporal array used for adding to data
        $temp = array();
        if($status ==  "PENDING" || $status == "PROCESSING")
        {
            //I go through the products of the order
            foreach($or['orderLines']  as  $line)
            {
                //I extract the stock
                $stock= $this->getStock("gtin", $line['gtin']);
                $temp = [
                    'identifierType' => 'gtin',
                    'identifier' =>  $line['gtin'],
                    'quantity' =>  $stock
                ];
                array_push($data,$temp);
            }
        }
        return $this->service->putHttp('/orders/'.$order.'/lines', $data);
    }

    //get current stock from DB
    private function getStock($idType, $id)
    {
        $result= VpStock::select("stock")->where($idType,"=",$id)->first();
        return $result['stock'];
    }

    public function refundMoney($order)
    {
        //Get order info
        $or= $this->show($order);
        $status= $or['status'];


        if($status == "SHIPPED") //A refund is allowed when status of order is SHIPPED
        {
            $data=[
                "linesToRefund"=> [                
                    "identifierType"=> "gtin",
                    "identifier"=> "string"            
                ],
                "shippingCost"=> 0,
                "amount"=> 0,
                "currency"=> "EUR"
            ];
            return $this->service->postHttp('/orders/'.$order.'/refund', $data);
        }else
        {
            //error code = 422 - message = Unprocessable Entity - Expected errors are:-Refund not allowed (wrong order status)
        }        
    }

    public function returnProduct($order)
    {
        
        $or= $this->show($order);
        $status= $or['status'];


        if($status == "SHIPPED") //A refund is allowed when status of order is SHIPPED
        {
            $data = [
                [
                    "identifierType"=> "gtin",
                    "identifier"=> "string",
                    "quantity"=> 0,
                    "date"=> "timestamp",
                    "reason"=> "UNKNOWN"
                ]
            ];
            return $this->service->postHttp('/orders/'.$order.'/return', $data);
        }else
        {
            //error code = 422 - message = Unprocessable Entity - Expected errors are:-Refund not allowed (wrong order status)
        }
    }
}
