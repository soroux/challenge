<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Product;
use Database\Factories\AvailabilityFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Product::factory(10)->create()->each(function ($product) {
            $product->availabilities()->saveMany(Availability::factory(5)->create());
        });

    }
}
