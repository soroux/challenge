<?php

namespace App\Services;

use App\Factory\Company\CompanyFactoryInterface;
use App\Models\Company;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

class ProductService
{
    /**
     * @param $startDate
     * @param $endDate
     * @return Collection
     */
    public function searchProducts($startDate, $endDate): Collection
    {
        $companies = Company::all();
        $products = collect();
        $factory = app(CompanyFactoryInterface::class);
        foreach ($companies as $company) {
            try {
                $service = $factory->make($company->name);
                $companyProducts = $service->getProducts($startDate, $endDate);
                $products->add($companyProducts);
            } catch (BindingResolutionException $e) {
                logger('cant create company instance');
            }
        }
        return $products->flatten(1);

    }
}
