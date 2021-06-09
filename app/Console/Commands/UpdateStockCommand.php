<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Offers\OfferController;
use App\Services\Service;
use App\Models\VpStock;

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
        $this->offerCtrl->uploadStock();
    }

    public function compareData()
    {
        $listStock = VpStock::getDataToUpdate();
        $route = '/offers?shopChannelId=755';
        $data = $this->service->getHttp($route);
        $data = json_decode($data, true);
        foreach ($data as $value) {
            foreach ($listStock as $key => $stock) {
                \Log::critical($value['stock'].'   |   '.$stock->stock);
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
