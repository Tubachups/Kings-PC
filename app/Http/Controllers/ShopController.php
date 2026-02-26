<?php

namespace App\Http\Controllers;

use App\Models\BuildPost;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    //
    public function index()
    {
        return Inertia::render('Shop/Index', [
        ]);
    }

    public function contacts()
    {
        return Inertia::render('Shop/Contacts', [

        ]);
    }

    public function components()
    {
        // $products = Product::where('is_active', true)->with('category')->get();
        return Inertia::render('Shop/Components', [
            'products' => Inertia::lazy(fn () => Product::where('is_active', true)->orderBy('name', 'asc')->with('category')->get()),
        ]);
    }

    public function showByCategory(Category $category)
    {
        return Inertia::render('Shop/Category', [
            'products' => $category->products()->where('is_active', true)->orderBy('name', 'asc')->with('category')->get(),
        ]);
    }

    public function builds(Request $request): Response
    {
        $sort = $request->query('sort', 'newest');
        $minPrice = $request->query('min_price') !== null ? (int) $request->query('min_price') : null;
        $maxPrice = $request->query('max_price') !== null ? (int) $request->query('max_price') : null;

        return Inertia::render('Shop/Builds', [
            'builds' => Inertia::scroll(fn () => BuildPost::applySort($sort)->applyPriceRange($minPrice, $maxPrice)->paginate(12)),
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ]);
    }
}
