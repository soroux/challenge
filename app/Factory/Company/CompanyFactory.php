<?php

namespace App\Factory\Company;


use Exception;
use Illuminate\Support\Arr;
use App\Services\Company\TravelloCompanyService;
use App\Services\Company\CompanyServiceInterface;
use App\Services\Company\HeavenlyToursCompanyService;

class CompanyFactory implements CompanyFactoryInterface
{

    private array $companies = [];

    /**
     * @param $name
     *
     * @return CompanyServiceInterface
     *
     * @throws Exception
     */
    public function make($name): CompanyServiceInterface
    {
        $service = Arr::get($this->companies, $name);

        // No need to create the service every time
        if ($service) {
            return $service;
        }

        $createMethod = 'create' . ucfirst($name) . 'CompanyService';
        if (!method_exists($this, $createMethod)) {
            throw new Exception("Company $name is not supported");
        }

        $service = $this->{$createMethod}();

        $this->companies[$name] = $service;

        return $service;
    }

    /**
     * Due to the chance of creating a microservice for this feature .
     * @return TravelloCompanyService
     */
    private function createTravelloCompanyService(): TravelloCompanyService
    {
        return new TravelloCompanyService();
    }

    /**
     * @return HeavenlyToursCompanyService
     */
    private function createHeavenlyTourCompanyService(): HeavenlyToursCompanyService
    {
        return new HeavenlyToursCompanyService();
    }


}
