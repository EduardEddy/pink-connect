<?php

namespace App\Console\Commands;

use App\Http\Controllers\Order\OrderController;
use App\Models\VpOrder;
use App\Services\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class updateOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recorre los pedidos y actualiza en la base de datos';
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
        Log::info("comenzando order: ".Carbon::now());
        
        //Listamos los nuevos pedidos que se adicionarán a la base de datos
        $newOrders = $this->getNewOrders();
        try {
            foreach($newOrders as $newOrder) {
                $this->orderCtrl->add_order($newOrder);
            }
        } catch (\Throwable $th) {
            Log::critical($th->getMessage());
        }
        Log::info("finalizando order: ".Carbon::now());

                
    }

    public function getNewOrders()
    {
        $outList = array();//return
        $lastMonth = mktime(0, 0, 0, date("m")-1, date("d"), date("Y")-1);
        $datelte = date('Y-m-d\TH:i:s');
        $dategte= date('Y-m-d\TH:i:s',$lastMonth);
        $limit = 100;
        // listamos los pedidos de pink-conect
        $route = '/orders?updateDateGTE='.$dategte.
                '&updateDateLTE='.$datelte.
                '&shopChannelId='.$this->shopId.
                '&limit='.$limit;
        $route = '/orders?updateDateGTE=&shopChannelId='.$this->shopId;
        $data = $this->service->getHttp($route);
        $data = json_decode($data, true);// decodificamos los datos

        // recorremos ambos listados los de pink connect y luego la lista de DB
        foreach ($data as $value) {
            $exist = VpOrder::where('id','=',$value['orderId'])->exists();
            if($exist) {
                $VP = VpOrder::find($value['orderId']);
                if($VP->status != $value['status']){
                    $this->orderCtrl->updateStatusTransaction($value['status'], $value['orderId']);
                }
            }else {
                array_push($outList,$value);
            } 
        }        
 
        return $outList;
    }
}
