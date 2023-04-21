<?php

namespace App\Factory\Company;


use App\Services\Clients\HeavenlyTour;
use App\Services\Company\CompanyServiceInterface;
use App\Services\Company\HeavenlyToursCompanyService;
use App\Services\Company\TravelloCompanyService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class CompanyFactory implements CompanyFactoryInterface
{

    private $companies = [];

    public function make($name): CompanyServiceInterface
    {
        $service = Arr::get($this->companies, $name);

        // No need to create the service every time
        if ($service) {
            return $service;
        }

        $createMethod = 'create' . ucfirst($name) . 'CompanyService';
        if (!method_exists($this, $createMethod)) {
            throw new \Exception("Company $name is not supported");
        }

        $service = $this->{$createMethod}();

        $this->companies[$name] = $service;

        return $service;
    }

    private function createTravelloCompanyService(): TravelloCompanyService
    {
        return new TravelloCompanyService();
    }

    private function createHeavenlyTourCompanyService(): HeavenlyToursCompanyService
    {
        return new HeavenlyToursCompanyService();
    }


}
