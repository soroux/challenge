<?php

namespace App\Http\Controllers\Api;

use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchProductRequest;

class SearchProductsController extends Controller
{
    private ProductService $productService;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param SearchProductRequest $request
     * @return JsonResponse
     */
    public function searchProduct(SearchProductRequest $request): JsonResponse
    {
        $inputs['startDate'] = $request->startDate ?: now()->toDateString();
        $inputs['endDate'] = $request->startDate ?: now()->addWeeks(2)->toDateString();
        $products = $this->productService->searchProducts($inputs['startDate'], $inputs['endDate']);

        return response()->json($products, 200);
    }
}
