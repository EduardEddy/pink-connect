<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
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

    public function updateOrderStatus($order ,$status)
    {
        //Get order info
        $or= $this->show($order);//se consultará a la db
        $data = [];
        if($status == "SHIPPED")
        {
            // $data = [
            //     'trackingNumber' => $or['trackingNumber'],
            //     'trackingUrl' => $or['trackingUrl'],
            //     'carrierId' => $or['carrierId']
            // ];
            $data = [
                'trackingNumber' => 1,
                'trackingUrl' => "url",
                'carrierId' => 2
            ];   
        }
        if($status == "CANCELLED")
        {
            $data += ['reason' => $or['reason']];
        }
        return $data;
        // return $this->service->putHttp('/orders/'.$order.'/status/'.$status, $data);
    }

    public function cancelProducts($order)
    {
        //Get order info
        $or= $this->show($order);
        $status =$or['status']; 
        $data = [];
        if($status ==  "Pending" || $status == "Processing")
        {
            foreach($or['orderLines']  as  $line)
            {
                $data += [
                    'identifierType' => $line['identifierType'],
                    'identifier' =>  $line['identifier'],
                    'quantity' =>  $line['quantity']
                ];
            }           
            return $this->service->putHttp('/orders/'.$order.'/lines', $data);            
        }else
        {
            //waiting
        }
        
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
