<?php

namespace App\Services\Company;

use App\Models\Product;
use App\Services\DTOs\ProductSearchObject;
use Illuminate\Support\Facades\Http;

class TravelloCompanyService implements CompanyServiceInterface
{


    public function getProducts($startDate, $endDate)
    {

        $query = Product::query();
        $query = $this->checkAvailabilityAndMinPrice($query, $startDate, $endDate);
        $products = $query->get();


        return $this->makeProductSearchObject($products);


    }

    private function checkAvailabilityAndMinPrice($query, $startDate, $endDate)
    {
        return $query->withMin('availabilities', 'price')->whereHas('availabilities', function ($availability) use ($startDate, $endDate) {
            $availability->where('start_time', '>=', $startDate)->where('end_time', '<=', $endDate);
        });
    }

    private function makeProductSearchObject($products)
    {
        $searchResults = collect();
        foreach ($products as $product) {
            $searchResult = new ProductSearchObject($product->name, $product->availabilities_min_price, $product->thumbnail);
            $searchResults->add($searchResult);
        }
        return $searchResults;
    }


}
