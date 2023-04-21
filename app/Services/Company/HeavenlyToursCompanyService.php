<?php

namespace App\Services\Company;

use App\Services\Clients\HeavenlyTour;
use App\Services\DTOs\ProductSearchObject;
use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HeavenlyToursCompanyService implements CompanyServiceInterface
{

    public $heavenlyTour;

    public function __construct()
    {
        $this->heavenlyTour = new HeavenlyTour();
    }

    public function getProducts($startDate, $endDate): \Illuminate\Support\Collection
    {
        $dates = $this->resolve_dates($startDate, $endDate);
        $allProductsInSelectedDates = $this->getAllProductsInDates($dates);
        $result = $this->makeProductSearchObject($allProductsInSelectedDates);

        return $result;
    }


    /**
     * convert date period to date instances in array
     */
    private function resolve_dates($startDate, $endDate)
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        return $period->toArray();
    }

    /**
     * get all products from third party api and check availabilities
     */
    private function getAllProductsInDates($dates): \Illuminate\Support\Collection
    {
        $productsInDates = collect();
        foreach ($dates as $date) {
            $availableProductsPerDate = Cache::remember('heavenly-tours-products-' . $date, 1440 * 60, function () use ($date, $productsInDates) {
                $productsPerDate = $this->heavenlyTour->getToursByDate($date);

                return $this->checkAvailability($productsPerDate, $date);
            });
            $productsInDates->add($availableProductsPerDate);
        }
        return $productsInDates->flatten(1)->groupBy('tourId');
    }

    private function checkAvailability($products, $date)
    {
        if ($products) {
            foreach ($products as $key => $tour) {
                $availability = $this->heavenlyTour->getTourAvailability($tour, $date);
                if (!$availability) {
                    $products->forget($key);
                }
            }
        }
        return $products;
    }

    /**
     * create dto object collection
     */
    private function makeProductSearchObject($allProductsInSelectedDates): \Illuminate\Support\Collection
    {
        $searchResults = collect();
        foreach ($allProductsInSelectedDates as $id => $allProductsInSelectedDate) {
            $product = collect();
           $tour = $this->heavenlyTour->getTourInfo($id);
            if ($tour) {
                $product->title = $tour['title'];
                $product->thumbnail = $this->getThumbnail($tour['photos'])['url'];
                $product->minPrice = $allProductsInSelectedDate->min('price');
                $searchResult = new ProductSearchObject($product->title, $product->minPrice, $product->thumbnail);
                $searchResults->add($searchResult);
            }

        }

        return $searchResults;
    }

    private function getThumbnail($photosObjects)
    {
        return collect($photosObjects)->where('type', 'thumbnail')->first();
    }
}
