<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['name', 'category']);

        $products = Product::query()
            ->with('category')
            ->filter($filters)
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => Category::all(),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->createProduct(
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

        return Inertia::render('Admin/Products/Edit', [
            'product' => $productData,
            'categories' => Category::all(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->updateProduct($product, $request->validated());

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

        return Inertia::render('Admin/Products/Archived', [
            'products' => $products,
            'filters' => $filters,
        ]);
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

        return back()->with('success', count($ids) . ' product(s) restored successfully.');
    }

    public function bulkForceDelete(Request $request): RedirectResponse
    {
        $ids = $request->validate(['ids' => 'required|array|exists:products,id'])['ids'];
        Product::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        return back()->with('success', count($ids) . ' product(s) permanently deleted.');
    }

    public function bulkArchive(Request $request): RedirectResponse
    {
        $ids = $request->validate(['ids' => 'required|array|exists:products,id'])['ids'];
        Product::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids) . ' product(s) archived successfully.');
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|exists:products,id',
            'is_active' => 'required|boolean',
        ]);

        Product::whereIn('id', $validated['ids'])->update(['is_active' => $validated['is_active']]);

        $status = $validated['is_active'] ? 'activated' : 'deactivated';
        return back()->with('success', count($validated['ids']) . " product(s) {$status} successfully.");
    }
}
