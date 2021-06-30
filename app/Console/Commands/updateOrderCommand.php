<?php

namespace App\Console\Commands;

use App\Http\Controllers\Order\OrderController;
use App\Models\VpOrder;
use App\Services\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateOrderCommand extends Command
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
        //sacamos las ordenes del mes
        $lastMonth = mktime(0, 0, 0, date("m")-1, date("d"), date("Y")-1);
        $dategte= date('Y-m-d\TH:i:s',$lastMonth);//desde
        $datelte = date('Y-m-d\TH:i:s');//hasta
        $limit = 50;//limite de registros por consulta
        $offset = 0;//cantidad de registros para saltarse por consulta

        // listamos los pedidos de pink-conect
        $route = '/orders?shopChannelId='.$this->shopId.
                '&updateDateLTE='.$datelte.
                '&updateDateGTE='.$dategte.
                '&offset='.$offset.
                '&limit='.$limit;
        $data = $this->service->getHttp($route);
        $data = json_decode($data, true);// decodificamos los datos
        //Mientras retorne datos compararemos los datos de Pinck-conect con la BD
        while($data){
            // recorremos ambos listados los de pink connect y luego la lista de DB
            foreach ($data as $value) {

                $exist = VpOrder::where('id','=',$value['orderId'])->exists();
                if(!$exist) {
                    array_push($outList,$value);
                }
                else { 
                    //en caso de que exista el pedido pero el estado sea diferente y que aparezca actualizado en la BD
                    $VP = VpOrder::find($value['orderId']);
                    if($VP->status != $value['status'] && $VP->updated==1){
                        $this->orderCtrl->updateStatusTransaction($value['status'], $value['orderId']);
                    }
                }
            }
            //Sumamos el limite con el offset para recorrer los otros 50 pedidos en la siguiente iteración
            $offset = $offset + $limit;
            $route = '/orders?shopChannelId='.$this->shopId.
                '&updateDateLTE='.$datelte.
                '&updateDateGTE='.$dategte.
                '&offset='.$offset.
                '&limit='.$limit;
            $data = $this->service->getHttp($route);
            $data = json_decode($data, true);// decodificamos los datos        
        }
        return $outList;        
    }
}
