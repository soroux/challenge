<?php

namespace App\Factory\Company;
use App\Services\Company\CompanyServiceInterface;

interface CompanyFactoryInterface
{
    public function make($name): CompanyServiceInterface;
}
