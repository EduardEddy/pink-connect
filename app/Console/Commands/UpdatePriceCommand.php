<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Offers\OfferController;
use App\Services\Service;
use App\Models\VpPrice;

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
        self::compareData();
        $this->offerCtrl->priceList();
    }
    
    public function compareData()
    {
        $listPrice = VpPrice::getDataToUpdate();
        $route = '/offers?shopChannelId='.$this->shopId;
        $data = $this->service->getHttp($route);
        $data = json_decode($data, true);
        foreach ($data as $value) {
            foreach ($listPrice as $key => $price) {
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
