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
        $route = '/offers?shopChannelId=755';
        $data = $this->service->getHttp($route);
        $data = json_decode($data, true);
        foreach ($data as $value) {
            foreach ($listPrice as $key => $price) {
                if ($value['gtin'] == $price->gtin && $value['manufacturerRecommendedPrice'] == $price->manufacturer_recommended_price) {
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
