<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;

class ShopController extends Controller
{
    //
    public function index() 
    {
        return Inertia::render('Shop/Index', [
        ]);
    }

    public function components()
    {
        $products = Product::where('is_active', true)->with('category')->get();
        return Inertia::render('Shop/Components', [
            'products' => $products,
        ]);
    }

    public function showByCategory(Category $category)
    {   
        $products = $category->products()->where('is_active', true)->with('category')->get();
        return Inertia::render('Shop/Category', [
            'products' => $products,
        ]);
    }
}
