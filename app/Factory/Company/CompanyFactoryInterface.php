<?php

namespace App\Factory\Company;
use App\Services\Company\CompanyServiceInterface;

interface CompanyFactoryInterface
{
    /**
     * @param $name
     * @return CompanyServiceInterface
     */
    public function make($name): CompanyServiceInterface;
}
