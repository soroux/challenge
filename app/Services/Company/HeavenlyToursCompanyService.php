<?php

namespace App\Services\Company;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Services\Clients\HeavenlyTour;
use App\Services\DTOs\ProductSearchObject;

class HeavenlyToursCompanyService implements CompanyServiceInterface
{

    private HeavenlyTour $heavenlyTour;

    public function __construct()
    {
        $this->heavenlyTour = new HeavenlyTour();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return Collection
     */
    public function getProducts($startDate, $endDate): Collection
    {
        $dates = $this->resolve_dates($startDate, $endDate);
        $allProductsInSelectedDates = $this->getAllProductsInDates($dates);
        return $this->makeProductSearchObject($allProductsInSelectedDates);
    }


    /**
     * @param $startDate
     * @param $endDate
     * convert date period to date instances in array
     * @return CarbonInterface[]
     */
    private function resolve_dates($startDate, $endDate): array
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        return $period->toArray();
    }

    /**
     * @param $dates
     * get all products from third party api and check availabilities
     * @return Collection
     */
    private function getAllProductsInDates($dates): Collection
    {
        $productsInDates = collect();
        foreach ($dates as $date) {
            $availableProductsPerDate = Cache::remember('heavenly-tours-products-' . $date, 1440 * 60, function () use ($date) {
                $productsPerDate = $this->heavenlyTour->getToursByDate($date);
                if ($productsPerDate) {
                    return $this->checkAvailability($productsPerDate, $date);
                }
                return null;
            });
            $productsInDates->add($availableProductsPerDate);
        }
        return $productsInDates->flatten(1)->groupBy('tourId');
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

    /**
     * @param $allProductsInSelectedDates
     * create dto object collection
     * @return Collection
     */
    private function makeProductSearchObject($allProductsInSelectedDates): Collection
    {
        $searchResults = collect();
        foreach ($allProductsInSelectedDates as $id => $allProductsInSelectedDate) {
            $product = collect();
            $tour = $this->heavenlyTour->getTourInfo($id);
            if ($tour) {
                $product->title = $tour['title'];
                $product->thumbnail = $this->getThumbnail($tour['photos']);
                $product->minPrice = $allProductsInSelectedDate->min('price');
                $searchResult = new ProductSearchObject($product->title, $product->minPrice, $product->thumbnail);
                $searchResults->add($searchResult);
            }

        }

        return $searchResults;
    }

    /**
     * @param $photosObjects
     * @return mixed
     */
    private function getThumbnail($photosObjects): mixed
    {
        return collect($photosObjects)->where('type', 'thumbnail')->first()['url'];
    }
}
