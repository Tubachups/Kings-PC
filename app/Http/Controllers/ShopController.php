<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateBuildRequest;
use App\Models\BuildPost;
use App\Models\Category;
use App\Models\Product;
use App\Services\AiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

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
            IF THE USER INPUTS 30000 BUDGET TRY TO REACH THAT BUDGET AS MUCH AS POSSIBLE.
            If impossible, return {"build_ids":[],"total_price":0,"explanation":"No valid build within budget"}
            Output ONLY JSON:
            {
            "build_ids": [101, 205, 309],
            "total_price": 55000,
            "explanation": "Why this build fits the user's prompt."
            }
    SYS;

    public function __construct(private AiService $ai_service) {}

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

    public function builds(Request $request): InertiaResponse
    {
        $sort = $request->query('sort', 'newest');
        $minPrice = $request->query('min_price') !== null ? (int) $request->query('min_price') : null;
        $maxPrice = $request->query('max_price') !== null ? (int) $request->query('max_price') : null;

        return Inertia::render('Shop/Builds', [
            'builds' => Inertia::scroll(fn () => BuildPost::query()
                ->select([
                    'id',
                    'text',
                    'likes',
                    'created_at',
                    'image_preview_1',
                    'image_preview_2',
                    'image_preview_3',
                    'image_preview_4',
                ])
                ->selectRaw('image_preview_1_blob IS NOT NULL as has_image_preview_1_blob')
                ->selectRaw('image_preview_2_blob IS NOT NULL as has_image_preview_2_blob')
                ->selectRaw('image_preview_3_blob IS NOT NULL as has_image_preview_3_blob')
                ->selectRaw('image_preview_4_blob IS NOT NULL as has_image_preview_4_blob')
                ->applySort($sort)
                ->applyPriceRange($minPrice, $maxPrice)
                ->paginate(15)
                ->through(fn (BuildPost $buildPost): array => [
                    'id' => $buildPost->id,
                    'text' => $buildPost->text,
                    'likes' => $buildPost->likes,
                    'created_at' => $buildPost->created_at,
                    'image_preview_1' => $this->resolveBuildImageUrl($buildPost, 1),
                    'image_preview_2' => $this->resolveBuildImageUrl($buildPost, 2),
                    'image_preview_3' => $this->resolveBuildImageUrl($buildPost, 3),
                    'image_preview_4' => $this->resolveBuildImageUrl($buildPost, 4),
                ])),
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ]);
    }

    public function buildImage(int $buildPost, int $slot): HttpResponse
    {
        if (! in_array($slot, [1, 2, 3, 4], true)) {
            abort(404);
        }

        $filename = "facebook_images/{$buildPost}_preview_{$slot}.jpg";
        $sourceCacheKey = "build-image-source:{$buildPost}:{$slot}";

        if (Storage::exists($filename)) {
            Cache::put($sourceCacheKey, 'file', now()->addDay());
            $file = Storage::get($filename);

            return response($file, 200, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=31536000',
                'Content-Length' => (string) strlen($file),
            ]);
        }

        $cachedSource = Cache::get($sourceCacheKey);
        if ($cachedSource === 'missing') {
            abort(404);
        }

        if (is_string($cachedSource) && str_starts_with($cachedSource, 'url:')) {
            return redirect()->away(substr($cachedSource, 4));
        }

        $blobColumn = "image_preview_{$slot}_blob";
        $urlColumn = "image_preview_{$slot}";

        $post = BuildPost::query()->whereKey($buildPost)->first([$blobColumn, $urlColumn]);
        if (! $post) {
            Cache::put($sourceCacheKey, 'missing', now()->addMinutes(10));
            abort(404);
        }

        $blob = $post->{$blobColumn};
        if (is_string($blob) && $blob !== '') {
            Storage::put($filename, $blob);
            Cache::put($sourceCacheKey, 'file', now()->addDay());

            return response($blob, 200, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'public, max-age=31536000',
                'Content-Length' => (string) strlen($blob),
            ]);
        }

        $url = $post->{$urlColumn};
        if (is_string($url) && $url !== '') {
            Cache::put($sourceCacheKey, "url:{$url}", now()->addHour());

            return redirect()->away($url);
        }

        Cache::put($sourceCacheKey, 'missing', now()->addMinutes(10));
        abort(404);
    }

    private function resolveBuildImageUrl(BuildPost $buildPost, int $slot): ?string
    {
        $urlColumn = "image_preview_{$slot}";
        $hasBlobColumn = "has_image_preview_{$slot}_blob";

        $hasBlob = (bool) ($buildPost->{$hasBlobColumn} ?? false);
        $hasSourceUrl = is_string($buildPost->{$urlColumn}) && $buildPost->{$urlColumn} !== '';

        if (! $hasBlob && ! $hasSourceUrl) {
            return null;
        }

        return route('builds.image', ['buildPost' => $buildPost->id, 'slot' => $slot]);
    }

    public function builder()
    {
        return Inertia::render('Shop/Builder', [

        ]);
    }

    public function generate(GenerateBuildRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userBudget = (float) $validated['budget'];

        $requiredCategories = ['cpu', 'motherboard', 'ram', 'storage', 'psu'];
        $categories = Category::query()->get(['id', 'name', 'slug']);

        $resolvedData = $this->ai_service->resolveCategory(self::categoryAliases, $categories);
        $resolvedCategories = $resolvedData['resolvedCategories'];
        if (! empty($resolvedData['missingCategoryDefinitions'])) {
            return json_422('Category mapping is incomplete.', ['missing_category_definitions' => $resolvedData['missingCategoryDefinitions']]);
        }

        $filteredData = $this->ai_service->filterInventory($resolvedCategories, $requiredCategories, $userBudget);
        if (! empty($filteredData['missingRequiredInventory'])) {
            return json_422('Missing Required Category Inventory', ['missing_categories' => $filteredData['missingRequiredInventory'], 'diagnostics' => $filteredData['inventoryDiagnostics']]);
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

        return $this->askGemini($validated['prompt'], $inventoryString, $userBudget, $requiredCategoryIds);
    }

    public function askGemini(string $prompt, string $inventoryString, float $userBudget, array $requiredCategoryIds): JsonResponse
    {
        $systemInstructions = self::systemInstructions;

        try {
            $response = $this->ai_service->aiPrompt($systemInstructions, $prompt, $inventoryString, $userBudget);
            if ($response->failed()) {
                return json_500('OpenRouter error', ['details' => $response->json()]);
            }

            $content = data_get($response->json(), 'choices.0.message.content');
            $aiData = json_decode($content, true);
            if (! is_array($aiData)) {
                return json_500('Invalid AI JSON response', ['raw' => $content]);
            }

            $ids = $aiData['build_ids'] ?? [];
            $products = Product::whereIn('id', $ids)->get();
            $actualTotal = (float) $products->sum('price');
            $selectedCategoryIds = $products->pluck('category_id')->unique()->all();
            $missingRequiredCategories = array_values(array_diff($requiredCategoryIds, $selectedCategoryIds));
            if (! empty($missingRequiredCategories)) {
                return json_422('AI build is missing required parts.', ['missing_category_ids' => $missingRequiredCategories, 'build_ids' => $ids, 'raw' => $content]);
            }

            if ($actualTotal > $userBudget) {
                return json_500(
                    'AI build exceeds budget.',
                    [
                        'budget' => $userBudget,
                        'actual_total' => $actualTotal,
                        'build_ids' => $ids,
                        'raw' => $content,
                    ]
                );
            }

            return response()->json([
                'build' => $products,
                'total_price' => $actualTotal,
                'explanation' => $aiData['explanation'] ?? '',
            ]);
        } catch (Exception $e) {
            return json_500('AI Service Unavailable:'.$e->getMessage());
        }
    }
}
