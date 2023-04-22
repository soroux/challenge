<?php

namespace App\Services\Clients;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class HeavenlyTour
{
    public $url;

    public function __construct()
    {
        $this->url = Config::get('companies.heavenly_tour.url');

    }

    public function getAllTours()
    {
        try {
            $response = Http::get($this->url . 'tours');
            if ($response->successful()) {
                $tour = $response->collect();
                return $tour;
            }
        } catch (\Exception $exception) {
            logger('getTours endpoint HasError:'.$exception->getMessage());
        }
        return null;

    }

    public function getTourInfo($id)
    {
        try {
            $response = Http::get($this->url . 'tours/' . $id);
            if ($response->successful()) {
                $tour = $response->collect();
                return $tour;
            }
        } catch (\Exception $exception) {
            logger('getTourInfo endpoint HasError:'.$exception->getMessage());
        }
        return null;
    }

    public function getToursByDate($date)
    {
        try {
            $response = Http::get($this->url . 'tour-prices?travelDate=' . $date->toDateString());
            if ($response->successful()) {
                return $response->collect();
            }
        } catch (\Exception $exception) {
            logger('getToursByDate endpoint HasError:'.$exception->getMessage());
        }
        return null;

    }

    public function getTourAvailability($tour, $date)
    {
        try {
            $response = Http::get($this->url . 'tours/' . $tour['tourId'] . '/availability?travelDate=' . $date->toDateString());
            if ($response->successful()) {
                return $response->json('available');

            }
        } catch (\Exception $exception) {
            logger('getTourAvailability endpoint HasError:'.$exception->getMessage());

        }
        return null;

    }

}
