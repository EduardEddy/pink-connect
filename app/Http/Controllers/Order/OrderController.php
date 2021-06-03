<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\InvoicesRequest;

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

        //excecute the puthttp() service with the params and data as the
        return $this->service->putHttp('/orders/'.$order.'/status/'.$status, $data);
    }

    public function cancelProducts(Request $request)
    {
        //Get order info
        $order = $request->order;
        //get the status of the order
        $status = $this->show($order)['status'];

        //body schema
        // $data = [];
        $lines = $request->input();
        
        if($status ==  "PENDING" || $status == "PROCESSING")
        {
            // //I go through the products of the order
            // foreach($lines  as  $line)
            // {
            //     // //I extract the stock from DB
            //     // $stock= $this->getStock($line['identifierType'], $line['identifier']);
            //     // if($stock)
            //     // {
            //     //     if($line['quantity'] > $stock)
            //     //     {
            //             array_push($data,$line);
            //         // }
            //     // }        
            // }
            return $this->service->putHttp('/orders/'.$order.'/lines', $lines);
        }else{
            return 422;
        }

    }

    // //get current stock from DB
    // private function getStock($idType, $id)
    // {
    //     try {
    //         $result= VpStock::select("stock")->where($idType,"=",$id)->firstOrFail();
    //         return $result['stock'];
    //     } catch (\Throwable $th) {
    //         Log::critical($th->getMessage());
    //     }        
    // }

    public function refundMoney(Request $request)
    {
        //Get order info
        $order = $request->order;
        $or = $this->show($order);        
        $transactionId = $request->transactionId;
        $data = [];
        $status= $or['status'];

        //A refund is allowed when status of order is SHIPPED
        if($status == "SHIPPED") 
        {
            $data=[
                "linesToRefund"=> $request->linesToRefund,
                "shippingCost"=> $request->shippingCost,
                "amount"=> $request->amount,
                "currency"=> "EUR"
            ];
            if($request->currency)
            {
                $data['currency'] = $request->currency;
            }
            if($transactionId)
            {
                return $this->service->postHttp('/orders/'.$order.'/refund?transactionId='.$transactionId, $data);
            }else
            {
                return $this->service->postHttp('/orders/'.$order.'/refund', $data);
            }
        }
        else
        {
            return 422;
        }
    }

    public function returnProduct(Request $request)
    {
        //Get order info
        $order = $request->order;
        $data =[];
        $or = $this->show($order);
        $status= $or['status'];

        //A refund is allowed when status of order is SHIPPED
        if($status == "SHIPPED") 
        {
            $data=[
                "identifierType" => $request->identifierType,
                "identifier" => $request->identifier,
                "quantity" => $request->quantity,
                "reason" => $request->reason
            ];
            if($request->date)
            {
                $data["date"] = $request->date;
            }
            return $this->service->postHttp('/orders/'.$order.'/return', $data);
        }
        else
        {
            return 422;
        }        
    }

    public function sendInvoice(InvoicesRequest $request, $order)
    {
        if ($request->hasFile('invoice')) {
            $file = $request->file('invoice');
            $destinationPath = 'invoices/';
            $random = Str::random(40);
            $name = $random.'.'.$file->getClientOriginalExtension();
            $file->move($destinationPath, $name);
            return $this->service->postFileHttp('/orders/'.$order.'/invoice',$destinationPath.'/'.$name,'invoice');
        }
    }
}//end class
