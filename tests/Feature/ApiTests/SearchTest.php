<?php

namespace Tests\Feature\ApiTests;

use App\Factory\Company\CompanyFactoryInterface;
use App\Models\Availability;
use App\Models\Product;
use App\Services\DTOs\ProductSearchObject;
use Database\Seeders\CompanySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    function setup(): void
    {
        parent::setUp();
        $this->seed(CompanySeeder::class);
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
        $tourAvailabilityResponse = [
            "available" => true
        ];


        Http::fake([
            'http://161.35.193.238:3006/api/tours' => Http::response($AllToursResponse, 200, ['Headers']),
            'http://161.35.193.238:3006/api/tours/33e42d' => Http::response($getTourResponse, 200, ['Headers']),
            'http://161.35.193.238:3006/api/tour-prices?travelDate=*' => Http::response($toursInSelectedDateResponse, 200, ['Headers']),
            'http://161.35.193.238:3006/api/tours/33e42d/availability?travelDate=*' => Http::response($tourAvailabilityResponse, 200, ['Headers']),

        ]);
        Product::factory()->create()->each(function ($p) {
            $p->availabilities()->saveMany(Availability::factory()->createMany([
                ['price' => 100, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
                ['price' => 300, 'start_time' => now()->addDay()->toDateString(), 'end_time' => now()->addWeek()->toDateString()],
            ]));
        });
        Product::factory()->create(['name' => 'city_of_light'])->each(function ($p) {
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

    }

    public function test_search_api_returns_expected_json()
    {
        $this->get('/api/search')
            ->assertJsonIsArray()
            ->assertJsonStructure([
                '*' => [
                    'title',
                    'minimumPrice',
                    'thumbnail'
                ]
            ]);


    }

    public function test_search_api_should_validate_date()
    {
        $this->get('/api/search?startDate=2023-10-2&endDate=2023-10-1')
            ->assertInvalid(['endDate' => 'The end date must be a date after start date.']);

    }

    public function test_heavenly_tours_company_service_returns_only_available_products()
    {
        $this->get('api/search')
            ->assertJsonFragment(['title' => 'city_of_light'])
            ->assertJsonFragment(['title' => 'desert safari'])
            ->assertJsonMissing(['title' => 'hell']);

    }

    public function test_search_api_returns_minimum_price_for_available_products()
    {
        $this->get('api/search')
            ->assertJsonFragment([
                'title' => 'city_of_light',
                'minimumPrice' => 100,
            ])
            ->assertJsonFragment([
                'title' => 'desert safari',
                'minimumPrice' => 172,
            ])
            ->assertJsonMissing(['title' => 'city_of_light',
                'minimumPrice' => 300],true);


    }


}
