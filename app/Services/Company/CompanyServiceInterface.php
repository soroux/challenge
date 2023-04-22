<?php

namespace App\Services\Company;

interface CompanyServiceInterface
{
    public function getProducts($startDate,$endDate);
}
