<?php

namespace App\Console\Commands;

use App\Services\Clients\HeavenlyTour;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class CacheHeavenlyTourAvailableProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heavenlyTour:cacheAvailableProducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get products from api and check availabilities and cache them';

    private HeavenlyTour $heavenlyTour;

    /**
     * Execute the console command.
     *
     * @return boolean
     */
    public function handle(): bool
    {
        $this->info('start caching all products');

        $this->heavenlyTour = new HeavenlyTour();
        $startDate = now()->toDateString();
        $cachingDates = Config::get('companies.heavenly_tour.cachingDates', 14);

        $endDate = now()->addDays($cachingDates)->toDateString();
        $period = CarbonPeriod::create($startDate, $endDate);
        $this->output->progressStart(count($period));

        foreach ($period as $date) {
            $productsPerDate = $this->heavenlyTour->getToursByDate($date);
            $availableProductsPerDate = null;
            if ($productsPerDate) {
                $availableProductsPerDate = $this->checkAvailability($productsPerDate, $date);
            }
            Cache::put('heavenly-tours-products-' . $date, $availableProductsPerDate, now()->addDay());
            $this->output->progressAdvance();

        }
        $this->output->progressFinish();

        $this->info('end of caching all products');

        return true;
    }

    /**
     * @param $products
     * @param $date
     * @return mixed
     */
    private function checkAvailability($products, $date): mixed
    {
        foreach ($products as $key => $tour) {
            $availability = $this->heavenlyTour->getTourAvailability($tour, $date);
            if (!$availability) {
                $products->forget($key);
            }
        }
        return $products;
    }
}
