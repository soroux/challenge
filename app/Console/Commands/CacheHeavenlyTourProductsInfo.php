<?php

namespace App\Console\Commands;

use App\Services\Clients\HeavenlyTour;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheHeavenlyTourProductsInfo extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heavenlyTour:cacheProductsInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get products info from api and cache them';

    /**
     * Execute the console command.
     *
     * @return boolean
     */
    public function handle(): bool
    {
        $this->info('start caching all products info');

        $heavenlyTour = new HeavenlyTour();
        $allProducts = $heavenlyTour->getAllTours();
        if ($allProducts) {

            $this->output->progressStart(count($allProducts));

            foreach ($allProducts as $product) {
                $productInfo = $heavenlyTour->getTourInfo($product['id']);
                Cache::put('heavenly-tour-products-Info-' . $product['id'], $productInfo, now()->addDay());
                $this->output->progressAdvance();

            }
            $this->output->progressFinish();
        }


        $this->info('end of caching all products info');

        return true;
    }
}
