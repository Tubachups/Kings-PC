<?php

namespace App\Services\Shop;

use App\Models\Category;
use App\Models\Product;
use App\Services\AiService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class BuildGenerationService
{
    private const CATEGORY_ALIASES = [
        'motherboard' => ['motherboard'],
        'cpu' => ['cpu', 'processor'],
        'ram' => ['ram', 'memory'],
        'storage' => ['storage', 'storages', 'ssd/hdd', 'ssd', 'hdd'],
        'psu' => ['psu', 'power supply'],
        'case' => ['case', 'chassis'],
        'gpu' => ['gpu', 'graphics card', 'video card'],
        'cooling' => ['cooling', 'cpu cooler', 'cooler'],
    ];

    private const SYSTEM_INSTRUCTIONS = <<<'SYS'
            System Instruction:
            You are the Kings-PC Architect.
            You MUST only use products listed in Available Inventory.
            Available Inventory keys are named categories: cpu, motherboard, ram, storage, psu, gpu, case, cooling.
            Each product object has: id, name, price, specs.
            Hard rule: Sum of selected item prices must be <= Users Budget.
            MAXIMIZ THE USER'S INPUT BUDGET ACCORDING TO HIS NEEDS.
            BUT ALSO DONT GO VERY LOW FOR THE TOTAL.
            IF THE USER INPUTS 30000 BUDGET TRY TO REACH THAT BUDGET AS MUCH AS POSSIBLE.
            If impossible, return {"build_ids":[],"total_price":0,"explanation":"No valid build within budget"}
            Output ONLY JSON:
            {
            "build_ids": [101, 205, 309],
            "total_price": 55000,
            "explanation": "Why this build fits the user's prompt."
            }
    SYS;

    public function __construct(private AiService $aiService) {}

    public function generate(array $validated): JsonResponse
    {
        $userBudget = (float) $validated['budget'];
        $requiredCategories = ['cpu', 'motherboard', 'ram', 'storage', 'psu'];
        $categories = Category::query()->get(['id', 'name', 'slug']);

        $resolvedData = $this->aiService->resolveCategory(self::CATEGORY_ALIASES, $categories);
        $resolvedCategories = $resolvedData['resolvedCategories'];

        if (! empty($resolvedData['missingCategoryDefinitions'])) {
            return response()->json([
                'error' => 'Category mapping is incomplete.',
                'missing_category_definitions' => $resolvedData['missingCategoryDefinitions'],
            ], 422);
        }

        $filteredData = $this->aiService->filterInventory($resolvedCategories, $requiredCategories, $userBudget);
        if (! empty($filteredData['missingRequiredInventory'])) {
            return response()->json([
                'error' => 'Missing Required Category Inventory',
                'missing_categories' => $filteredData['missingRequiredInventory'],
                'diagnostics' => $filteredData['inventoryDiagnostics'],
            ], 422);
        }

        $inventoryString = json_encode($filteredData['filteredInventory']);
        if ($inventoryString === false) {
            return response()->json(['error' => json_last_error_msg()], 500);
        }

        $requiredCategoryIds = collect($requiredCategories)
            ->map(fn ($name) => $resolvedCategories[$name] ?? null)
            ->filter()
            ->values()
            ->all();

        return $this->askGemini(
            (string) $validated['prompt'],
            $inventoryString,
            $userBudget,
            $requiredCategoryIds
        );
    }

    public function askGemini(string $prompt, string $inventoryString, float $userBudget, array $requiredCategoryIds): JsonResponse
    {
        try {
            $response = $this->aiService->aiPrompt(self::SYSTEM_INSTRUCTIONS, $prompt, $inventoryString, $userBudget);
            if ($response->failed()) {
                return response()->json([
                    'error' => 'OpenRouter error',
                    'details' => $response->json(),
                ], 500);
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            $aiData = json_decode($content, true);
            if (! is_array($aiData)) {
                return response()->json([
                    'error' => 'Invalid AI JSON response',
                    'raw' => $content,
                ], 500);
            }

            $ids = $aiData['build_ids'] ?? [];
            $products = Product::whereIn('id', $ids)->get();
            $actualTotal = (float) $products->sum('price');
            $selectedCategoryIds = $products->pluck('category_id')->unique()->all();
            $missingRequiredCategories = array_values(array_diff($requiredCategoryIds, $selectedCategoryIds));

            if (! empty($missingRequiredCategories)) {
                return response()->json([
                    'error' => 'AI build is missing required parts.',
                    'missing_category_ids' => $missingRequiredCategories,
                    'build_ids' => $ids,
                    'raw' => $content,
                ], 422);
            }

            if ($actualTotal > $userBudget) {
                return response()->json([
                    'error' => 'AI build exceeds budget.',
                    'budget' => $userBudget,
                    'actual_total' => $actualTotal,
                    'build_ids' => $ids,
                    'raw' => $content,
                ], 500);
            }

            return response()->json([
                'build' => $products,
                'total_price' => $actualTotal,
                'explanation' => $aiData['explanation'] ?? '',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => 'AI Service Unavailable:'.$exception->getMessage(),
            ], 500);
        }
    }
}
