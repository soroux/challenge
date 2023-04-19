<?php

namespace Database\Seeders;

use App\Models\bank;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Company::insert([
            [
                'name' => 'travello',
            ],
            [
                'name' => 'heavenlyTour',
            ],
        ]);
    }
}
