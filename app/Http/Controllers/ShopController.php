<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Support\Facades\Redis;

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
            'products' => Inertia::lazy(fn () => Product::where('is_active', true)->with('category')->get()),
        ]);
    }

    public function showByCategory(Category $category)
    {   
        $products = $category->products()->where('is_active', true)->with('category')->get();
        return Inertia::render('Shop/Category', [
            'products' => $products,
        ]);
    }

    public function redis()
    {
        Redis::set('test_key', 'Redis is working!');
        return Redis::get('test_key');
    }

}
