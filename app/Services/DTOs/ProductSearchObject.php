<?php

namespace App\Services\DTOs;

class ProductSearchObject
{
    public float $minimumPrice;
    public string $title ;
    public string $thumbnail ;

    public function __construct(string $title,float $minimumPrice,string$thumbnail)
    {
        $this->title = $title;
        $this->minimumPrice = $minimumPrice;
        $this->thumbnail = $thumbnail;
    }

}
