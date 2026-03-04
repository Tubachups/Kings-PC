<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateBuildRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\Shop\BuildFeedService;
use App\Services\Shop\BuildGenerationService;
use App\Services\Shop\BuildImageService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ShopController extends Controller
{
    public function __construct(
        private BuildFeedService $buildFeedService,
        private BuildImageService $buildImageService,
        private BuildGenerationService $buildGenerationService
    ) {}

    public function index(): InertiaResponse
    {
        return Inertia::render('Shop/Index', [
            'topBuilds' => $this->buildFeedService->getTopBuilds(),
        ]);
    }

    public function contacts(): InertiaResponse
    {
        return Inertia::render('Shop/Contacts', [

        ]);
    }

    public function components(): InertiaResponse
    {
        return Inertia::render('Shop/Components', [
            'products' => Inertia::lazy(fn () => Product::where('is_active', true)->orderBy('name', 'asc')->with('category')->get()),
        ]);
    }

    public function showByCategory(Category $category): InertiaResponse
    {
        return Inertia::render('Shop/Category', [
            'products' => $category->products()->where('is_active', true)->orderBy('name', 'asc')->with('category')->get(),
        ]);
    }

    public function builds(Request $request): InertiaResponse
    {
        $sort = $request->query('sort', 'newest');
        $minPrice = $request->query('min_price') !== null ? (int) $request->query('min_price') : null;
        $maxPrice = $request->query('max_price') !== null ? (int) $request->query('max_price') : null;

        return Inertia::render('Shop/Builds', [
            'builds' => Inertia::scroll(fn () => $this->buildFeedService->getBuildFeed($sort, $minPrice, $maxPrice)),
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ]);
    }

    public function buildImage(int $buildPost, int $slot): HttpResponse
    {
        return $this->buildImageService->serveBuildImage($buildPost, $slot);
    }

    public function builder(): InertiaResponse
    {
        return Inertia::render('Shop/Builder', [

        ]);
    }

    public function generate(GenerateBuildRequest $request): JsonResponse
    {
        return $this->buildGenerationService->generate($request->validated());
    }
}
