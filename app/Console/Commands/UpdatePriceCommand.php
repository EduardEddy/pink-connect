<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Offers\OfferController;
use App\Services\Service;
use App\Models\VpPrice;
use Carbon\Carbon;

class UpdatePriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recorre la lista de precios por actualizar compara y actualiza los precios';
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
        \Log::info("comenzando price: ".Carbon::now());
        //llamamos a la funcion compare data para recorrer los registros en DB e igualarlos a true si los precios son iguales en pink conect
        self::compareData();
        //llamamos a la funcion priceList en el controlador offer para hacer la peticion post y cargar los registros igualados a false
        $this->offerCtrl->priceList();
        \Log::info("finalizando price: ".Carbon::now());
    }
    
    public function compareData()
    {
        $listPrice = VpPrice::getDataToUpdate();// obtenemos los datos desde la DB
        // armamos el endpoint para hacer la peticion GET
        $route = '/offers?shopChannelId='.$this->shopId;
        $data = $this->service->getHttp($route);

        $data = json_decode($data, true);// decodificamos los datos
        // recorremos ambos listados los de pink connect y luego la lista de DB
        foreach ($data as $value) {
            foreach ($listPrice as $key => $price) {
                //reemplazamos los caracteres para poder hacer la comparacion y evaluar si son iguales o no los datos 
                $manufacture = str_replace(" €","",$value['manufacturerRecommendedPrice']);
                $manufacture = str_replace(",",".",$manufacture);

                $sellingPrice = str_replace(" €","",$value['sellingPrice']);
                $sellingPrice = str_replace(",",".",$sellingPrice);
                
                if ($value['gtin'] == $price->gtin && floatval($manufacture) == floatval($price->manufacturer_recommended_price) && floatval($sellingPrice) == floatval($price->selling_price)) {
                    \DB::table('vp_prices')
                    ->where('gtin',$value['gtin'])
                    ->update([
                        'updated'=>true
                    ]);
                }
            }
        }
    }
}
