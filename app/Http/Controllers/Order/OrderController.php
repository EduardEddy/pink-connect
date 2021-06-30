<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Services\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\InvoicesRequest;
use App\Models\VpOrder;
use App\Models\VpOrderDeliveryDetail;
use App\Models\VpOrderLine;
use App\Models\VpOrderRefund;
use App\Models\VpOrderShippingInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service = new Service();
    }

    public function add_order($request){
        $order=$request['orderId'];
        $vpOrder = [
            'id'=>$order,
            'marketplaceName'=> $request['marketplaceName'],
            'marketplaceCode'=> $request['marketplaceCode'],
            'marketplaceOrderCode'=> $request['marketplaceOrderCode'],
            'shopChannelId'=> $request['shopChannelId'],
            'shopChannelName'=> $request['shopChannelName'],
            'status'=> $request['status'],
            'shippedOrderDate'=> $request['shippedOrderDate'],
            'totalPrice'=> $request['totalPrice'],
            'shippingCosts'=> $request['shippingCosts'],
            'shippingTaxRate'=> $request['shippingTaxRate'],
            'currency'=> $request['currency'],
            'requestedShippingMethod'=> $request['requestedShippingMethod'],
            'deliveryNote'=> $request['deliveryNote'],
            'pickupPointId'=> $request['pickupPointId'],
            "created_at" => $request['createOrderDate'],
            "updated_at" => $request['updateOrderDate'],
        ];
        
        //order lines
        if(array_key_exists('orderLines', $request))
        {
            $lines = array();
            $tempLines = $request['orderLines'];
            // $statusLines = array();
            foreach($tempLines as $line){
                $orderLine = [
                    'id' => $line['id'],
                    'order_id' => $order,
                    "gtin" => $line['gtin'],
                    "sku" => $line['sku'],
                    "name" => $line['gtin'],
                    "price" => $line['price'],
                    "taxRate" => $line['taxRate'],
                    "quantity" => $line['quantity'],
                    "brandName" => $line['brandName'],
                    "totalPrice" => $line['totalPrice'],
                    "manufacturerReference" => $line['manufacturerReference'],
                    "created_at" => Carbon::now('Europe/Madrid'),
                    "updated_at" => Carbon::now()
                                   
                ];
                $status = $line['status'];
                $statusLine = [
                    "in_stock" => array_key_exists('In stock',$status)?$status['In stock']:null,
                    "out_of_stock" => array_key_exists('Out of Stock',$status)?$status['Out of Stock']:null,
                    "shipped" => array_key_exists('Shipped',$status)?$status['Shipped']:0,
                    "returned" => array_key_exists('Returned',$status)?$status['Returned']:0,
                    "cancelled" => array_key_exists('Cancelled',$status)?$status['Cancelled']:0,
                    "processing" => array_key_exists('Processing',$status)?$status['Processing']:0,
                    "pending" => array_key_exists('Pending',$status)?$status['Pending']:0          
                ];
                

                $orderLine= $orderLine + $statusLine;
                array_push($lines,$orderLine);
            }            
        }
        //refunds
        if(array_key_exists('refunds', $request))
        {
            $refunds = array();
            $tempRefunds = $request['refunds'];
            foreach($tempRefunds as $ref){
                $refund = [
                    'order_id' => $order,
                    "date" => $ref['date'],
                    "productCost" => $ref['productCost'],
                    "shippingCost" => $ref['shippingCost'],
                    "currency" => $ref['currency'],
                    "type" => $ref['type'],
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now()
                ];
                array_push($refunds,$refund);
            }            
        }

        //deliveryDetails
        if(array_key_exists('deliveryDetails', $request))
        {
            $deliveryDetails = array();
            $tempDel = $request['deliveryDetails'];
            foreach($tempDel as $del){
                $deliveryDetail = [
                    'order_id' => $order,
                    "carrierId" => $del['carrierId'],
                    "carrierName" => $del['carrierName'],
                    "trackingNumber" => $del['trackingNumber'],
                    "trackingUrl" => $del['trackingUrl'],
                    "created_at" => Carbon::now('Europe/Madrid'),
                    "updated_at" => Carbon::now('Europe/Madrid')
                ];
                array_push($deliveryDetails,$deliveryDetail);
            }            
        }

        //shippingInformation
        if(array_key_exists('shippingInformation', $request))
        {
            $shippingInformation = array();
            $tempShipInfo = $request['shippingInformation'];
            $shippingInformation = [
                'order_id' => $order,
                "name" => $tempShipInfo["name"],
                "company" => $tempShipInfo["company"],
                "address" => $tempShipInfo["address"],
                "city" => $tempShipInfo["city"],
                "state" => $tempShipInfo["state"],
                "zipCode" => $tempShipInfo["zipCode"],
                "country" => $tempShipInfo["country"],
                "countryIsoCode" => $tempShipInfo["countryIsoCode"],
                "email" => $tempShipInfo["email"],
                "phone" => $tempShipInfo["phone"],
                "comment" => $tempShipInfo["comment"],
                "created_at" => Carbon::now('Europe/Madrid'),
                "updated_at" => Carbon::now('Europe/Madrid')
            ];                     
        }
        return $this->addOrderTransaction($vpOrder, $lines, $refunds, $deliveryDetails, $shippingInformation);
    }

    private function addOrderTransaction ($vpOrder, $lines, $refunds, $deliveryDetails, $shippingInformation)
    {
        return DB::transaction(function() use ($vpOrder, $lines, $refunds, $deliveryDetails, $shippingInformation) {
            
            VpOrder::insert($vpOrder);            
            VpOrderLine::insert($lines);
            if($refunds)
            {
                VpOrderRefund::insert($refunds);    
            }
            if($deliveryDetails)
            {
                VpOrderDeliveryDetail::insert($deliveryDetails);    
            }
            if($shippingInformation)
            {
                VpOrderShippingInfo::insert($shippingInformation);    
            }            
        });
    }

    public function index(Request $request)
    {
        //first check if there is query params
        $query = http_build_query($request->query());
        if($query)
        {
            $response = $this->service->getHttp('/orders?'.$query);
        }else
        {
            $response = $this->service->getHttp('/orders');
        }
        if($response->successful()){
            return $response->json();
        }else
        {    
            return $this->service->errorResponse($response);
        }
    }

    public function show($order)
    {
        $response = $this->service->getHttp('/orders/'.$order);
        if($response->successful()){
            return $response->json();
        }else
        {    
            return $this->service->errorResponse($response);
        }
    }

    public function updateOrderStatus(Request $request)
    {
        //get the info from the request
        $order = $request->order;
        $status = $request->status;

        //body schema
        $data = $request->input();
                   
        $response = $this->service->putHttp('/orders/'.$order.'/status/'.$status, $data);
        if($response->successful()){
            $this->updateStatusTransaction($status,$order);
            return ['code' => 204, 'message' => "OK-The resource was successfully updated"];
        }else
        {    
            return $this->service->errorResponse($response);
        }
    }
    public function updateStatusTransaction($status, $order){
        return DB::transaction(function() use ($status, $order) {  
            $vpOrder = VpOrder::find($order); 
            if($vpOrder)
            {
                $vpOrder->status = $status;                
                if($status == "SHIPPED"){
                    $vpOrder->shippedOrderDate = Carbon::now('Europe/Madrid')->toDateTime();
                }
                

                $vpLines = $vpOrder->orderLine()->get();
                foreach ($vpLines as $vpLine) {
                    $vpLine->changeStatus($status);
                    $vpLine->save();                    
                }
                $vpOrder->save(); 
            }            
        });        
    }

    public function updateOrderStatusOnVp($status, $vpDeliveryDetail, $vpOrder)
    {
        //body schema
        $order = $vpDeliveryDetail->order_id;
        $data = [
            "trackingNumber"=> $vpDeliveryDetail->trackingNumber,
            "trackingUrl"=> $vpDeliveryDetail->trackingUrl,
            "carrierId"=> $vpDeliveryDetail->carrierId,
        ]; 

        //vp response
        $response = $this->service->putHttp('/orders/'.$order.'/status/'.$status, $data);

        if($response->successful()) {
            $this->updateStatusVpTransaction($status, $vpOrder, $vpDeliveryDetail);
            return ['code' => 204, 'message' => "OK-The resource was successfully updated"];
        }else {    
            Log::info("The order:". $order." wasn't updated");
            return $this->service->errorResponse($response);
        }
    }

    public function updateStatusVpTransaction($status, $vpOrder, $vpDeliveryDetail){
        return DB::transaction(function() use ($status, $vpOrder, $vpDeliveryDetail) {  
            if($vpOrder)
            {
                $vpOrder->status = $status;
                $vpDeliveryDetail->setUpdated();
                
                $vpLines = $vpOrder->orderLine()->get();
                foreach ($vpLines as $vpLine) {
                    $vpLine->changeStatus($status);
                    $vpLine->save();                    
                }
                $vpOrder->setUpdated();
                $vpOrder->save(); 
            }            
        });        
    }

    public function cancelProducts(Request $request)
    {
        //Get order info
        $order = $request->order;

        //body schema
        $lines = $request->input();        
        
        //make request
        $response = $this->service->putHttp('/orders/'.$order.'/lines', $lines);
        if($response->successful()){
            $this->cancelProductsTransaction($lines,$order);
            return ['code' => 204, 'message' => "OK-The products were successfully cancelled"];
        }else
        {    
            return $this->service->errorResponse($response);
        }    
    }

    private function cancelProductsTransaction($lines, $order){
        return DB::transaction(function() use ($lines, $order) {            
            foreach ($lines as $line) {  
                $vpLine = VpOrderLine::where($line['identifierType'], "=", $line['identifier'], "and" , "order_id", "=",$order )
                    ->update(['cancelled'=>$line['quantity']]);
            }
        });        
    }


    public function refundMoney(Request $request)
    {
        //Get order info
        $order = $request->order;
        $transactionId = $request->transactionId;
        $data = [];

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
            $response = $this->service->postHttp('/orders/'.$order.'/refund?transactionId='.$transactionId, $data);
        }else
        {
            $response = $this->service->postHttp('/orders/'.$order.'/refund', $data);
        }
        if($response->successful()){       
            $this->refundMoneyTransaction($data, $order);   
            return ['code' => 204, 'message' => "OK-The refund was successfully created"];
        }else
        {    
            return $this->service->errorResponse($response);
        }

    }

    private function refundMoneyTransaction($data, $order){
        return DB::transaction(function() use ($data, $order) {  
            $VpOrderRefund = new VpOrderRefund([
                'order_id' => $order,
                'date' => Carbon::now('Europe/Madrid')->toDateTime(),
                'productCost' => $data['amount'],
                'shippingCost' => $data['shippingCost'],
                'currency' => $data['currency'],
                'type' => "REFUND"
            ]);
            $VpOrderRefund->save();
        });        
    }

    public function returnProduct(Request $request)
    {
        //Get order info
        $order = $request->order;
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
        $response = $this->service->postHttp('/orders/'.$order.'/return', $data);
        if($response->successful()){            
            $this->returnTransaction($data, $order);
            return ['code' => 204, 'message' => "OK-The return was successfully created"];
        }else
        {    
            return $this->service->errorResponse($response);
        }  
    }

    private function returnTransaction($data, $order){
        return DB::transaction(function() use ($data, $order) {            
            $vpLine = VpOrderLine::getLine($data['identifierType'], $data['identifier'],$order);
            foreach ($vpLine as $vline) {
                $vline['shipped'] = $vline['shipped']-$data['quantity'];
                $vline['returned'] = $vline['returned']+$data['quantity'];
                $vline->save();
            }
            
            
        });        
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
