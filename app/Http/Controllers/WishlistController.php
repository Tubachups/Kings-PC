<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class WishlistController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $products = Product::query()
            ->whereHas('wishlistItems', function ($query) use ($request): void {
                $query->where('user_id', $request->user()->id);
            })
            ->with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('shop/Wishlist', [
            'products' => $products,
        ]);
    }

    public function toggle(Request $request, Product $product): RedirectResponse
    {
        $userId = $request->user()->id;

        $existingItem = WishlistItem::query()
            ->where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($existingItem) {
            $existingItem->delete();

            return back()->with('success', 'Removed from your wishlist.');
        }

        WishlistItem::query()->create([
            'user_id' => $userId,
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Added to your wishlist.');
    }
}
