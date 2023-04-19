<?php

namespace App\Services;

use App\Factory\Company\CompanyFactoryInterface;
use App\Models\Company;
use Illuminate\Contracts\Container\BindingResolutionException;

class ProductService
{


    public function searchProducts($startDate, $endDate)
    {
        $companies = Company::all();
        $products = collect();
        foreach ($companies as $company) {
            $factory = app(CompanyFactoryInterface::class);
            try {
                $service = $factory->make($company->name);
            } catch (BindingResolutionException $e) {
                logger('cant create company instance');
            }

            $companyProducts = $service->getProducts($startDate, $endDate);
            $products->add($companyProducts);
        }
        return $products->flatten(1);

    }
}
