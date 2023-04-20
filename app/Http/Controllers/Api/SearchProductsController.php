<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class SearchProductsController extends Controller
{
    //
    //
    /**
     * @param ProductService $balanceService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    public function searchProduct(SearchProductRequest $request)
    {
        $inputs['startDate'] = $request->startDate ?: now()->toDateString();
        $inputs['endDate'] = $request->startDate ?: now()->addWeeks(2)->toDateString();
        $products = $this->productService->searchProducts($inputs['startDate'], $inputs['endDate']);

        return response()->json($products, 200);
    }
}
