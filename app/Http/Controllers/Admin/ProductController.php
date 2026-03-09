<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Services\admin\PendingOrderService;
use App\Services\productservice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private productservice $productservice,
        private PendingOrderService $pendingOrderService
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['name', 'category']);

        $products = Product::query()
            ->with('category')
            ->filter($filters)
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/products/Index', [
            'products' => $products,
            'categories' => Category::all(),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/products/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productservice->createProduct(
            $request->validated(),
            $request->file('image')
        );

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): Response
    {
        $productData = array_merge($product->toArray(), [
            'image' => $product->image_url,
        ]);

        return Inertia::render('admin/products/Edit', [
            'product' => $productData,
            'categories' => Category::all(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productservice->updateProduct($product, $request->validated());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function updateStatus(Request $request, Product $product): RedirectResponse
    {
        $product->update($request->validate(['is_active' => 'required|boolean']));

        return back()->with('success', 'Product status updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product archived successfully.');
    }

    // --- ARCHIVE & BULK ACTIONS ---

    public function archived(Request $request): Response
    {
        $filters = $request->only(['name']);

        $products = Product::onlyTrashed()
            ->with('category')
            ->filter($filters)
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/products/Archived', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    public function pendingOrders(Request $request): Response
    {
        $perPage = (int) $request->integer('per_page', 12);
        $perPage = max(6, min($perPage, 48));

        return Inertia::render('admin/products/PendingOrders', [
            'orders' => $this->pendingOrderService->getPendingOrdersPaginated($perPage)
                ->withQueryString(),
        ]);
    }

    public function processedOrders(Request $request): Response
    {
        $perPage = (int) $request->integer('per_page', 12);
        $perPage = max(6, min($perPage, 48));

        return Inertia::render('admin/products/ProcessedOrders', [
            'orders' => $this->pendingOrderService->getProcessedOrdersPaginated($perPage)
                ->withQueryString(),
        ]);
    }

    public function shippedOrders(Request $request): Response
    {
        $perPage = (int) $request->integer('per_page', 12);
        $perPage = max(6, min($perPage, 48));

        return Inertia::render('admin/products/ShippedOrders', [
            'orders' => $this->pendingOrderService->getShippedOrdersPaginated($perPage)
                ->withQueryString(),
        ]);
    }

    public function deliveredOrders(Request $request): Response
    {
        $perPage = (int) $request->integer('per_page', 12);
        $perPage = max(6, min($perPage, 48));

        return Inertia::render('admin/products/DeliveredOrders', [
            'orders' => $this->pendingOrderService->getDeliveredOrdersPaginated($perPage)
                ->withQueryString(),
        ]);
    }

    public function advancePendingOrder(Order $order): RedirectResponse
    {
        $nextStatus = match ($order->status) {
            'Pending', 'Order Placed' => 'Processing',
            'Processing' => 'Shipped',
            'Shipped' => 'Delivered',
            default => null,
        };

        if ($nextStatus === null) {
            return back()->with('error', 'Order is already at its final status.');
        }

        $order->update(['status' => $nextStatus]);

        return back()->with('success', "Order {$order->order_number} moved to {$nextStatus}.");
    }

    public function restore(int $id): RedirectResponse
    {
        Product::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Product restored successfully.');
    }

    public function forceDelete(int $id): RedirectResponse
    {
        Product::onlyTrashed()->findOrFail($id)->forceDelete();

        return back()->with('success', 'Product permanently deleted.');
    }

    public function bulkRestore(Request $request): RedirectResponse
    {
        $ids = $request->validate(['ids' => 'required|array|exists:products,id'])['ids'];
        Product::onlyTrashed()->whereIn('id', $ids)->restore();

        return back()->with('success', count($ids).' product(s) restored successfully.');
    }

    public function bulkForceDelete(Request $request): RedirectResponse
    {
        $ids = $request->validate(['ids' => 'required|array|exists:products,id'])['ids'];
        Product::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        return back()->with('success', count($ids).' product(s) permanently deleted.');
    }

    public function bulkArchive(Request $request): RedirectResponse
    {
        $ids = $request->validate(['ids' => 'required|array|exists:products,id'])['ids'];
        Product::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids).' product(s) archived successfully.');
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|exists:products,id',
            'is_active' => 'required|boolean',
        ]);

        Product::whereIn('id', $validated['ids'])->update(['is_active' => $validated['is_active']]);

        $status = $validated['is_active'] ? 'activated' : 'deactivated';

        return back()->with('success', count($validated['ids'])." product(s) {$status} successfully.");
    }
}
