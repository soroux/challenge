<?php

namespace Tests\Feature\CompanyServices;

use App\Factory\Company\CompanyFactoryInterface;
use App\Models\Availability;
use App\Models\Product;
use App\Services\DTOs\ProductSearchObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelloCompanyServiceTest extends TestCase
{
    use RefreshDatabase;

    function setup(): void
    {
        parent::setUp();

    }

    public function test_travello_company_service_returns_minimum_price_for_available_products()
    {
        Product::factory()->create()->each(function ($p) {
            $p->availabilities()->saveMany(Availability::factory()->createMany([
                ['price' => 100, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
                ['price' => 300, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
                ['price' => 500, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
                ['price' => 700, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
            ]));
        });

        $factory = app(CompanyFactoryInterface::class);
        $service = $factory->make('travello');
        $products = $service->getProducts(now()->toDateString(), now()->addWeeks(2)->toDateString());

        $this->assertEquals(100, $products->first()->minimumPrice);

    }

    public function test_travello_company_service_returns_only_available_products()
    {
        Product::factory()->create(['name' => 'city-of-lights'])->each(function ($p) {
            $p->availabilities()->saveMany(Availability::factory()->createMany([
                ['price' => 100, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
                ['price' => 300, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
                ['price' => 500, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
                ['price' => 700, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
            ]));
        });
        Product::factory()->create(['name' => 'hell'])->each(function ($p) {
            $p->availabilities()->saveMany(Availability::factory()->createMany([
                ['price' => 100, 'start_time' => now()->addYear()->toDateString(), 'end_time' => now()->addCenturies()->toDateString()],

            ]));
        });

        $factory = app(CompanyFactoryInterface::class);
        $service = $factory->make('travello');
        $products = $service->getProducts(now()->toDateString(), now()->addWeeks(2)->toDateString());

        $this->assertEmpty($products->where('title', 'hell')->first());

    }

    public function test_travello_company_service_returns_DTO_collection()
    {
        Product::factory()->create()->each(function ($p) {
            $p->availabilities()->saveMany(Availability::factory()->createMany([
                ['price' => 100, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
                ['price' => 300, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],

            ]));
        });
        Product::factory()->create(['name' => 'hell'])->each(function ($p) {
            $p->availabilities()->saveMany(Availability::factory()->createMany([
                ['price' => 100, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],

            ]));
        });

        $factory = app(CompanyFactoryInterface::class);
        $service = $factory->make('travello');
        $products = $service->getProducts(now()->toDateString(), now()->addWeeks(2)->toDateString());


        foreach ($products as $product) {
            $this->assertInstanceOf(ProductSearchObject::class, $product);
        }

    }

}
