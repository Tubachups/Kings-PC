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
        'cpu'         => ['cpu', 'processor'],
        'ram'         => ['ram', 'memory'],
        'storage'     => ['storage', 'storages', 'ssd/hdd', 'ssd', 'hdd'],
        'psu'         => ['psu', 'power supply'],
        'case'        => ['case', 'chassis'],
        'gpu'         => ['gpu', 'graphics card', 'video card'],
        'cooling'     => ['cooling', 'cpu cooler', 'cooler'],
    ];

    private const CATEGORY_BUDGET_WEIGHTS = [
        'cpu'         => 0.25,
        'motherboard' => 0.20,
        'ram'         => 0.12,
        'storage'     => 0.10,
        'psu'         => 0.08,
        'gpu'         => 0.30,   // optional but takes the largest slice when present
        'case'        => 0.08,
        'cooling'     => 0.07,
    ];

    private const SYSTEM_INSTRUCTIONS = <<<'SYS'
        You are the Kings-PC Architect. Select PC parts exclusively from the Available Inventory below.

        RULES (violating any rule is a failure):
        1. Only use products from Available Inventory — never invent parts.
        2. Required categories: cpu, motherboard, ram, storage, psu — you MUST include one from each.
        3. Optional categories: gpu, case, cooling — include them only when budget allows.
        4. Each product already has a "max_price" field. You MUST NOT pick a product whose price
           exceeds its max_price. (They are pre-filtered, so just pick the highest-priced option
           that best fits the user's needs.)
        5. BUDGET GOAL: Maximise the total price so it is as close to Total Budget as possible
           without going over. Target = 100 % of Total Budget.
        6. Ensure component compatibility (CPU socket ↔ motherboard, RAM type/speed, PSU wattage).
        7. If no valid build is possible, return:
           {"build_ids":[],"total_price":0,"explanation":"No valid build within budget"}

        Respond with ONLY raw JSON — no markdown fences:
        {
          "build_ids": [101, 205, 309],
          "total_price": 29750,
          "explanation": "Short reason this build fits the user's needs and budget."
        }
    SYS;

    public function __construct(private AiService $aiService) {}

    public function generate(array $validated): JsonResponse
    {
        $userBudget         = (float) $validated['budget'];
        $requiredCategories = ['cpu', 'motherboard', 'ram', 'storage', 'psu'];
        $categories         = Category::query()->get(['id', 'name', 'slug']);

        $resolvedData       = $this->aiService->resolveCategory(self::CATEGORY_ALIASES, $categories);
        $resolvedCategories = $resolvedData['resolvedCategories'];

        if (! empty($resolvedData['missingCategoryDefinitions'])) {
            return response()->json([
                'error'                        => 'Category mapping is incomplete.',
                'missing_category_definitions' => $resolvedData['missingCategoryDefinitions'],
            ], 422);
        }

        // Build per-category budget caps before querying inventory.
        $categoryBudgets = $this->allocateCategoryBudgets($userBudget, array_keys($resolvedCategories));

        $filteredData = $this->aiService->filterInventory(
            $resolvedCategories,
            $requiredCategories,
            $userBudget,
            $categoryBudgets
        );

        if (! empty($filteredData['missingRequiredInventory'])) {
            return response()->json([
                'error'              => 'Missing Required Category Inventory',
                'missing_categories' => $filteredData['missingRequiredInventory'],
                'diagnostics'        => $filteredData['inventoryDiagnostics'],
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

        return $this->askAi(
            (string) $validated['prompt'],
            $inventoryString,
            $userBudget,
            $requiredCategoryIds
        );
    }

    private function allocateCategoryBudgets(float $totalBudget, array $presentCategories): array
    {
        $budgets = [];

        foreach ($presentCategories as $category) {
            $weight            = self::CATEGORY_BUDGET_WEIGHTS[$category] ?? 0.10;
            $budgets[$category] = round($totalBudget * $weight, 2);
        }

        return $budgets;
    }

    public function askAi(
        string $prompt,
        string $inventoryString,
        float  $userBudget,
        array  $requiredCategoryIds
    ): JsonResponse {
        try {
            $response = $this->aiService->aiPrompt(self::SYSTEM_INSTRUCTIONS, $prompt, $inventoryString, $userBudget);

            if ($response->failed()) {
                return response()->json([
                    'error'   => 'OpenRouter error',
                    'details' => $response->json(),
                ], 500);
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            $aiData  = json_decode($content, true);

            if (! is_array($aiData)) {
                return response()->json([
                    'error' => 'Invalid AI JSON response',
                    'raw'   => $content,
                ], 500);
            }

            $ids      = $aiData['build_ids'] ?? [];
            $products = Product::whereIn('id', $ids)->get();

            // --- Validation layer (safety net, should rarely trigger now) ---

            $actualTotal             = (float) $products->sum('price');
            $selectedCategoryIds     = $products->pluck('category_id')->unique()->all();
            $missingRequiredCategories = array_values(array_diff($requiredCategoryIds, $selectedCategoryIds));

            if (! empty($missingRequiredCategories)) {
                return response()->json([
                    'error'                => 'AI build is missing required parts.',
                    'missing_category_ids' => $missingRequiredCategories,
                    'build_ids'            => $ids,
                    'raw'                  => $content,
                ], 422);
            }



            $budgetUtilization = $userBudget > 0 ? round(($actualTotal / $userBudget) * 100, 1) : 0;

            return response()->json([
                'build'              => $products,
                'total_price'        => $actualTotal,
                'budget'             => $userBudget,
                'budget_utilization' => "{$budgetUtilization}%",
                'explanation'        => $aiData['explanation'] ?? '',
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => 'AI Service Unavailable: '.$exception->getMessage(),
            ], 500);
        }
    }
}
