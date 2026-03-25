<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AiService
{
    public function resolveCategory($categoryAliases, $categories): array
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
            ->filter(fn ($key) => ! isset($resolvedCategories[$key]))
            ->values()
            ->all();

        return [
            'resolvedCategories' => $resolvedCategories,
            'missingCategoryDefinitions' => $missingCategoryDefinitions,
        ];
    }

    public function filterInventory(
        array $resolvedCategories,
        array $requiredCategories,
        float $userBudget,
        array $categoryBudgets = []
    ): array {
        $filteredInventory = [];
        $inventoryDiagnostics = [];

        foreach ($resolvedCategories as $name => $categoryId) {
            $cap = $categoryBudgets[$name] ?? $userBudget;

            $baseQuery = Product::query()
                ->where('category_id', $categoryId)
                ->where('stock', '>', 0)
                ->where('price', '<=', $cap);

            $products = (clone $baseQuery)
                ->orderBy('price', 'desc')
                ->take(10)
                ->get(['id', 'name', 'price', 'specs', 'image_url']);

            $filteredInventory[$name] = $products->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->price,
                'specs' => $p->specs,
                'image_url' => $p->image_url,
                'max_price' => $cap,   // surfaced to the AI as a hard ceiling per slot
            ])->values();

            $inventoryDiagnostics[$name] = [
                'category_id' => $categoryId,
                'category_cap' => $cap,
                'total_products' => Product::query()->where('category_id', $categoryId)->count(),
                'in_stock_products' => Product::query()->where('category_id', $categoryId)->where('stock', '>', 0)->count(),
                'candidate_products' => count($filteredInventory[$name]),
            ];
        }

        $missingRequiredInventory = collect($requiredCategories)
            ->filter(fn ($cat) => empty($filteredInventory[$cat]) || count($filteredInventory[$cat]) === 0)
            ->values()
            ->all();

        return [
            'filteredInventory' => $filteredInventory,
            'inventoryDiagnostics' => $inventoryDiagnostics,
            'missingRequiredInventory' => $missingRequiredInventory,
        ];
    }

    public function aiPrompt(string $systemInstructions, string $prompt, string $inventoryString, float $userBudget): Response
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
                    [
                        'role' => 'user',
                        'content' => implode("\n\n", [
                            "User Prompt: {$prompt}",
                            "Available Inventory (each product already has price <= its category's max_price — pick the highest-priced option that fits the user's needs): {$inventoryString}",
                            "Total Budget: {$userBudget}",
                        ]),
                    ],
                ],
                'temperature' => 0.2,
                'response_format' => ['type' => 'json_object'],
            ]);
    }
}
