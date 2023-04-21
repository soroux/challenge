<?php

namespace Tests\Unit;

use App\Factory\Company\CompanyFactoryInterface;
use App\Services\Company\TravelloCompanyService;
use App\Services\Company\HeavenlyToursCompanyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyFactoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_Company_factory_creates_travello_Company_service(): void
    {
        $factory = app(CompanyFactoryInterface::class);
        $service = $factory->make('travello');
        $this->assertInstanceOf(TravelloCompanyService::class, $service);
    }

    /**
     * A basic feature test example.
     */
    public function test_Company_factory_creates_heavenly_tours_Company_service(): void
    {
        $factory = app(CompanyFactoryInterface::class);
        $service = $factory->make('heavenlyTour');
        $this->assertInstanceOf(HeavenlyToursCompanyService::class, $service);
    }

}
