<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function createProduct(array $data, ?UploadedFile $image): Product
    {
        $data['slug'] = Str::slug($data['name']);
        $data['specs'] = json_decode($data['specs'], true);
        $data['is_active'] = $data['is_active'] ?? false;

        if ($image) {
            $data['image_url'] = $image->store('components', 'public');
        }

        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data): bool
    {
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $data['is_active'] ?? false;

        if (isset($data['image'])) {
            $data['image_url'] = $data['image'];
            unset($data['image']);
        }

        return $product->update($data);
    }
}
