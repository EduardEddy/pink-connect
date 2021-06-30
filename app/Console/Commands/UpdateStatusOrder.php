<?php

namespace App\Console\Commands;

use App\Http\Controllers\Order\OrderController;
use App\Models\VpOrder;
use App\Models\VpOrderDeliveryDetail;
use App\Services\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateStatusOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:status_orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recorre los pedidos en la base de datos y actualiza en Pink-Connect';
    protected $service;
    protected $orderCtrl;
    protected $shopId;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->service = new Service();
        $this->orderCtrl = new OrderController();
        $this->shopId = env('SHOP_ID');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("comenzando actualizacion de estados de pedidos: ".Carbon::now());
        
        try {
            $this->updateStatus();
        } catch (\Throwable $th) {
            Log::critical($th->getMessage());
        }
        Log::info("finalizado la actualizacion de estados de pedidos: ".Carbon::now());
    }

    public function updateStatus()
    {
        // obtenemos los datos a actualizar desde la DB
        $vpOrdersUpdate = VpOrder::getDataToUpdate();
        
        foreach ($vpOrdersUpdate as $vpOrder) {
                //determinamos que deliveryDetails hay que actualizar
                $vpDeliveryDetail = $vpOrder->getDeliverDetailsToUpdate();
            
                foreach ($vpDeliveryDetail as  $det) {
                    $this->orderCtrl->updateOrderStatusOnVp($vpOrder->status, $det, $vpOrder);
            }            
        }   
    }
}
