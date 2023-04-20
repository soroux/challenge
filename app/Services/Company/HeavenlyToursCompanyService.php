<?php

namespace App\Services\Company;

use App\Services\DTOs\ProductSearchObject;
use Carbon\CarbonPeriod;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HeavenlyToursCompanyService implements CompanyServiceInterface
{

    private $url;

    public function getProducts($startDate, $endDate): \Illuminate\Support\Collection
    {
        $dates = $this->resolve_dates($startDate, $endDate);
        $allProductsInSelectedDates = $this->getAllProductsInDates($dates);
        $result = $this->makeProductSearchObject($allProductsInSelectedDates);

        return $result;
    }

    public function setConfig($config)
    {
        $this->url = $config['url'];
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
            $productsPerDate = Cache::remember('heavenly-tours-products-' . $date, 1440 * 60, function () use ($date, $productsInDates) {
                $response = Http::get($this->url . 'tour-prices?travelDate=' . $date->toDateString());
                if ($response->successful()) {
                    $productsPerDate = $response->collect();
                    $productsPerDate = $this->checkAvailability($productsPerDate, $date);

                }
                return $productsPerDate;
            });
            $productsInDates->add($productsPerDate);
        }
        return $productsInDates->flatten(1)->groupBy('tourId');
    }

    private function checkAvailability($products, $date)
    {

        foreach ($products as $key => $tour) {
            $response = Http::get($this->url . 'tours/' . $tour['tourId'] . '/availability?travelDate=' . $date->toDateString());
            if ($response->successful()) {
                if ($response->json('available') == false) {
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
            $response = Http::get($this->url . 'tours/' . $id);
            if ($response->successful()) {
                $product->title = $response->json(['title']);
                $product->thumbnail = $this->getThumbnail($response->json(['photos']))['url'];
                $product->minPrice = $allProductsInSelectedDate->min('price');
            }
            $searchResult = new ProductSearchObject($product->title, $product->minPrice, $product->thumbnail);
            $searchResults->add($searchResult);
        }

        return $searchResults;
    }

    private function getThumbnail($photosObjects)
    {
        return collect($photosObjects)->where('type', 'thumbnail')->first();
    }
}
