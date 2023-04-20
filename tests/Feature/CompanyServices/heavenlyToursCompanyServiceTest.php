<?php

namespace Tests\Feature\CompanyServices;

use App\Factory\Company\CompanyFactoryInterface;
use App\Services\DTOs\ProductSearchObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class heavenlyToursCompanyServiceTest extends TestCase
{
    use RefreshDatabase;

    function setup(): void
    {
        parent::setUp();
        $AllToursResponse = [
            [
                "id" => "acdf-asefaw2342",
                "title" => "city tour",
                "excerpt" => "lorem ipsum",
                "city" => "Ankara"
            ],
            [
                "id" => "33e42d",
                "title" => "desert safari",
                "excerpt" => "lorem ipsum",
                "city" => "Dubai"
            ],
            [
                "id" => "33e432dd",
                "title" => "At the top burj khalifa",
                "excerpt" => "lorem ipsum",
                "city" => "Dubai"
            ],
            [
                "id" => "33e432dd",
                "title" => "museum of future",
                "excerpt" => "lorem ipsum",
                "city" => "Abu Dhabi"
            ]
        ];
        $getTourResponse = [
            "id" => "33e42d",
            "title" => "desert safari",
            "excerpt" => "lorem ipsum",
            "city" => "Dubai",
            "description" => "<ul><li>3 hours</li><li>Dubai Desert</li></ul>",
            "categories" => [
                "Adventure",
                "Outdoor",
                "Safari"
            ],
            "photos" => [
                [
                    "type" => "thumbnail",
                    "url" => "https://picsum.photos/200/300"
                ],
                [
                    "type" => "gallery",
                    "url" => "https://picsum.photos/200"
                ]
            ]
        ];

        $toursInSelectedDateResponse = [

            [
                "tourId" => "33e42d",
                "price" => 172
            ]

        ];

        Http::fake([
            'http://161.35.193.238:3006/api/tours' => Http::response($AllToursResponse, 200, ['Headers']),
            'http://161.35.193.238:3006/api/tours/33e42d' => Http::response($getTourResponse, 200, ['Headers']),
            'http://161.35.193.238:3006/api/tour-prices?travelDate=*' => Http::response($toursInSelectedDateResponse, 200, ['Headers']),
        ]);

    }

    public function test_heavenly_tours_company_service_returns_minimum_price_for_available_products()
    {

        $tourAvailabilityResponse = [
            "available" => true
        ];
        Http::fake([
            'http://161.35.193.238:3006/api/tours/33e42d/availability?travelDate=*' => Http::response($tourAvailabilityResponse, 200, ['Headers']),
        ]);
        $factory = app(CompanyFactoryInterface::class);
        $service = $factory->make('heavenlyTour');
        $products = $service->getProducts(now()->toDateString(), now()->addWeeks(2)->toDateString());
        $this->assertEquals(172, $products->where('title','desert safari')->first()->minimumPrice);

    }

    public function test_heavenly_tours_company_service_returns_only_available_products()
    {
        $tourAvailabilityResponse = [
            "available" => false
        ];
        Http::fake([
            'http://161.35.193.238:3006/api/tours/33e42d/availability?travelDate=*' => Http::response(json_encode($tourAvailabilityResponse), 200, ['Headers']),
        ]);

        $factory = app(CompanyFactoryInterface::class);
        $service = $factory->make('heavenlyTour');
        $products = $service->getProducts(now()->toDateString(), now()->addWeeks(2)->toDateString());
        $this->assertEmpty($products->first());

    }

    public function test_heavenly_tours_company_service_returns_DTO_collection()
    {
        $tourAvailabilityResponse = [
            "available" => true
        ];
        Http::fake([
            'http://161.35.193.238:3006/api/tours/33e42d/availability?travelDate=*' => Http::response($tourAvailabilityResponse, 200, ['Headers']),
        ]);

        $factory = app(CompanyFactoryInterface::class);
        $service = $factory->make('heavenlyTour');
        $products = $service->getProducts(now()->toDateString(), now()->addWeeks(2)->toDateString());


        foreach ($products as $product) {
            $this->assertInstanceOf(ProductSearchObject::class, $product);
        }

    }

}
