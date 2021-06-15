<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Offers\OfferController;
use App\Services\Service;
use App\Models\VpStock;
use Carbon\Carbon;

class UpdateStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recorre la lista de Stock y los actualiza';
    protected $service;
    protected $offerCtrl;
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
        $this->offerCtrl = new OfferController();
        $this->shopId = env('SHOP_ID');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("comenzando stock: ".Carbon::now());
        //llamamos a la funcion compare data para recorrer los registros en DB e igualarlos a true si los precios son iguales en pink conect
        self::compareData();
        //llamamos a la funcion priceList en el controlador offer para hacer la peticion post y cargar los registros igualados a false
        $this->offerCtrl->uploadStock();
        \Log::info("finalizando stock: ".Carbon::now());
    }

    public function compareData()
    {
        $listStock = VpStock::getDataToUpdate();// obtenemos los datos desde la DB
        // armamos el endpoint para hacer la peticion GET
        $route = '/offers?shopChannelId='.$this->shopId;
        $data = $this->service->getHttp($route);

        $data = json_decode($data, true);// decodificamos los datos
        // recorremos ambos listados los de pink connect y luego la lista de DB
        foreach ($data as $value) {
            foreach ($listStock as $key => $stock) {
                //comparacion y evaluacion de los datos para determinar si son iguales o no 
                if ($value['gtin'] == $stock->gtin && $value['stock'] == $stock->stock) {
                    \DB::table('vp_stocks')
                    ->where('gtin',$value['gtin'])
                    ->update([
                        'updated'=>true
                    ]);
                }
            }
        }
    }
}
