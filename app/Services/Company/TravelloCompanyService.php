<?php

namespace App\Services\Company;

use App\Models\Product;
use Illuminate\Support\Collection;
use App\Services\DTOs\ProductSearchObject;

class TravelloCompanyService implements CompanyServiceInterface
{

    /**
     * @param $startDate
     * @param $endDate
     * @return Collection
     */
    public function getProducts($startDate, $endDate): Collection
    {
        $query = Product::query();
        $query = $this->checkAvailabilityAndMinPrice($query, $startDate, $endDate);
        $products = $query->get();

        return $this->makeProductSearchObject($products);
    }

    /**
     * @param $query
     * @param $startDate
     * @param $endDate
     * check availabilities and get add min price parameter
     * @return mixed
     */
    private function checkAvailabilityAndMinPrice($query, $startDate, $endDate): mixed
    {
        return $query->withMin('availabilities', 'price')->whereHas('availabilities', function ($availability) use ($startDate, $endDate) {
            $availability->where('start_time', '>=', $startDate)->where('end_time', '<=', $endDate);
        });
    }

    /**
     * @param $products
     * create dto object
     * @return Collection
     */
    private function makeProductSearchObject($products): Collection
    {
        $searchResults = collect();
        foreach ($products as $product) {
            $searchResult = new ProductSearchObject($product->name, $product->availabilities_min_price, $product->thumbnail);
            $searchResults->add($searchResult);
        }
        return $searchResults;
    }
}
