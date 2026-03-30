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
