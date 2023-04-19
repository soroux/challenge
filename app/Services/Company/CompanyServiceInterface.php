<?php

namespace App\Services\Company;

use Faker\Core\Number;

interface CompanyServiceInterface
{
    public function getProducts($startDate,$endDate);
}
