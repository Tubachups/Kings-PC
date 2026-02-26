<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AiService
{

    public function resolveCategory($categoryAliases, $categories) : array
    {
        $resolveCategoryId = function (array $aliases) use ($categories): ?int {
            $normalizedAliases = array_map(
                fn ($value) => strtolower(trim((string) $value)),
                $aliases
            );

            foreach ($categories as $category) {
                $name = strtolower(trim((string) $category->name));
                $slug = strtolower(trim((string) $category->slug));

                if (in_array($name, $normalizedAliases, true) || in_array($slug, $normalizedAliases, true)) {
                    return (int) $category->id;
                }
            }

            return null;
        };

        $resolvedCategories = [];
        foreach ($categoryAliases as $key => $aliases) {
            $categoryId = $resolveCategoryId($aliases);
            if ($categoryId !== null) {
                $resolvedCategories[$key] = $categoryId;
            }
        }

        $missingCategoryDefinitions = collect(array_keys($categoryAliases))
            ->filter(fn ($key) => !isset($resolvedCategories[$key]))
            ->values()
            ->all();

        return [
            'resolvedCategories'            => $resolvedCategories, 
            'missingCategoryDefinitions'    => $missingCategoryDefinitions
        ];
    }

    public function filterInventory($resolvedCategories, $requiredCategories, $userBudget) : array
    {
        $filteredInventory = [];
        $inventoryDiagnostics = [];

        foreach ($resolvedCategories as $name => $categoryId) {
            $baseQuery = Product::query()
                ->where('category_id', $categoryId)
                ->where('stock', '>', 0);

            $products = (clone $baseQuery)
                ->where('price', '<=', $userBudget * 0.5)
                ->orderBy('price', 'asc')
                ->take(8)
                ->get(['id', 'name', 'price', 'specs']);

            if ($products->isEmpty()) {
                $products = (clone $baseQuery)
                    ->orderBy('price', 'asc')
                    ->take(8)
                    ->get(['id', 'name', 'price', 'specs']);
            }

            $filteredInventory[$name] = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'specs' => $product->specs,
                ];
            })->values();

            $inventoryDiagnostics[$name] = [
                'category_id' => $categoryId,
                'total_products' => Product::query()->where('category_id', $categoryId)->count(),
                'in_stock_products' => (clone $baseQuery)->count(),
                'candidate_products' => count($filteredInventory[$name]),
            ];
        }

        $missingRequiredInventory = collect($requiredCategories)
            ->filter(fn ($categoryName) => empty($filteredInventory[$categoryName]) || count($filteredInventory[$categoryName]) === 0)
            ->values()
            ->all();

        return [
            'filteredInventory'         => $filteredInventory,
            'inventoryDiagnostics'      => $inventoryDiagnostics,
            'missingRequiredInventory'  => $missingRequiredInventory
        ];
    }

    public function aiPrompt($systemInstructions, $prompt, $inventoryString, $userBudget) : Response
    {
        return Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer '.config('services.openrouter.api_key'),
                    'HTTP-Referer' => config('services.openrouter.referer'),
                    'X-Title' => config('services.openrouter.title'),
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => config('services.openrouter.model'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemInstructions],
                        ['role' => 'user', 'content' => "User Prompt: {$prompt}\n\nAvailable Inventory: {$inventoryString}\n\nUsers Budget: {$userBudget}"],
                    ],
                    'temperature' => 0.2,
                    'response_format' => ['type' => 'json_object'],
                ]);
    }

}
