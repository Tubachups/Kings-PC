<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateBuildRequest;
use App\Models\BuildPost;
use App\Models\Category;
use App\Models\Product;
use App\Services\AiService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inertia\Response;

class ShopController extends Controller
{
    private const categoryAliases = [
            'motherboard' => ['motherboard'],
            'cpu' => ['cpu', 'processor'],
            'ram' => ['ram', 'memory'],
            'storage' => ['storage', 'storages', 'ssd/hdd', 'ssd', 'hdd'],
            'psu' => ['psu', 'power supply'],
            'case' => ['case', 'chassis'],
            'gpu' => ['gpu', 'graphics card', 'video card'],
            'cooling' => ['cooling', 'cpu cooler', 'cooler'],
    ];
    private const systemInstructions = <<<'SYS'
            System Instruction:
            You are the Kings-PC Architect.
            You MUST only use products listed in Available Inventory.
            Available Inventory keys are named categories: cpu, motherboard, ram, storage, psu, gpu, case, cooling.
            Each product object has: id, name, price, specs.
            Hard rule: Sum of selected item prices must be <= Users Budget.
            A valid build MUST include at least cpu, motherboard, ram, storage, and psu.
            DO NOT GO OVER BUDGET.
            BUT ALSO DONT GO VERY LOW FOR THE TOTAL.
            If impossible, return {"build_ids":[],"total_price":0,"explanation":"No valid build within budget"}
            Output ONLY JSON:
            {
            "build_ids": [101, 205, 309],
            "total_price": 55000,
            "explanation": "Why this build fits the user's prompt."
            }
    SYS;

    public function __construct(private AiService $ai_service){}

    public function index()
    {
        return Inertia::render('Shop/Index', [
            'topBuilds' => BuildPost::query()
                ->applySort('price')
                ->limit(6)
                ->get([
                    'id',
                    'text',
                    'image_preview_1',
                    'image_preview_2',
                    'image_preview_3',
                    'image_preview_4',
                    'likes',
                    'created_at',
                ]),
        ]);
    }

    public function contacts()
    {
        return Inertia::render('Shop/Contacts', [

        ]);
    }

    public function components()
    {
        // $products = Product::where('is_active', true)->with('category')->get();
        return Inertia::render('Shop/Components', [
            'products' => Inertia::lazy(fn () => Product::where('is_active', true)->orderBy('name', 'asc')->with('category')->get()),
        ]);
    }

    public function showByCategory(Category $category)
    {
        return Inertia::render('Shop/Category', [
            'products' => $category->products()->where('is_active', true)->orderBy('name', 'asc')->with('category')->get(),
        ]);
    }

    public function builds(Request $request): Response
    {
        $sort = $request->query('sort', 'newest');
        $minPrice = $request->query('min_price') !== null ? (int) $request->query('min_price') : null;
        $maxPrice = $request->query('max_price') !== null ? (int) $request->query('max_price') : null;

        return Inertia::render('Shop/Builds', [
            'builds' => Inertia::scroll(fn () => BuildPost::applySort($sort)->applyPriceRange($minPrice, $maxPrice)->paginate(15)),
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ]);
    }

    public function builder()
    {
        return Inertia::render('Shop/Builder', [

        ]);
    }

    public function generate(GenerateBuildRequest $request) : JsonResponse
    {
        $validated = $request->validated();
        $userBudget = (float) $validated['budget'];

        $requiredCategories = ['cpu', 'motherboard', 'ram', 'storage', 'psu'];
        $categories = Category::query()->get(['id', 'name', 'slug']);

        $resolvedData = $this->ai_service->resolveCategory(self::categoryAliases, $categories);
        $resolvedCategories = $resolvedData['resolvedCategories'];
        if (!empty($resolvedData['missingCategoryDefinitions'])) return json_422('Category mapping is incomplete.', ['missing_category_definitions' => $resolvedData['missingCategoryDefinitions']]);

        $filteredData = $this->ai_service->filterInventory($resolvedCategories, $requiredCategories, $userBudget);
        if (!empty($filteredData['missingRequiredInventory'])) return json_422('Missing Required Category Inventory', ['missing_categories' => $filteredData['missingRequiredInventory'], 'diagnostics' => $filteredData['inventoryDiagnostics']]);


        $inventoryString = json_encode($filteredData['filteredInventory']);
        if ($inventoryString === false) return response()->json(['error' => json_last_error_msg()], 500);

        $requiredCategoryIds = collect($requiredCategories)
            ->map(fn ($name) => $resolvedCategories[$name] ?? null)
            ->filter()
            ->values()
            ->all();

        return $this->askGemini($validated['prompt'], $inventoryString, $userBudget, $requiredCategoryIds);
    }

    public function askGemini(string $prompt, string $inventoryString, float $userBudget, array $requiredCategoryIds) : JsonResponse
    {
        $systemInstructions = self::systemInstructions;

        try {
            $response = $this->ai_service->aiPrompt($systemInstructions, $prompt, $inventoryString, $userBudget);
            if ($response->failed()) return json_500('OpenRouter error', ['details' => $response->json()]);

            $content = data_get($response->json(), 'choices.0.message.content');
            $aiData = json_decode($content, true);
            if (!is_array($aiData)) return json_500('Invalid AI JSON response', ['raw' => $content]);

            $ids = $aiData['build_ids'] ?? [];
            $products = Product::whereIn('id', $ids)->get();
            $actualTotal = (float) $products->sum('price');
            $selectedCategoryIds = $products->pluck('category_id')->unique()->all();
            $missingRequiredCategories = array_values(array_diff($requiredCategoryIds, $selectedCategoryIds));
            if (!empty($missingRequiredCategories)) return json_422('AI build is missing required parts.', ['missing_category_ids' => $missingRequiredCategories, 'build_ids' => $ids, 'raw' => $content,]);


            if ($actualTotal > $userBudget) {
                return json_500(
                    'AI build exceeds budget.',
                    [
                        'budget' => $userBudget,
                        'actual_total' => $actualTotal,
                        'build_ids' => $ids,
                        'raw' => $content
                    ]
                );
            }

            return response()->json([
                'build' => $products,
                'total_price' => $actualTotal,
                'explanation' => $aiData['explanation'] ?? '',
            ]);
        } catch (Exception $e) {
            return json_500('AI Service Unavailable:' . $e->getMessage());
        }
    }



}
