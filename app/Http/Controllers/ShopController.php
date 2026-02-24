<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

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

    public function redis()
    {
        Redis::set('test_key', 'Redis is working!');

        return Redis::get('test_key');
    }
}
