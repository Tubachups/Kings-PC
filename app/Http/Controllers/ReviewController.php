<?php

namespace App\Http\Controllers;

use App\Models\FacebookReview;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $reviews = FacebookReview::query()
            ->inRandomOrder()
            ->limit(30)
            ->get(['id', 'Name', 'Feedback']);

        return response()->json($reviews);
    }
}
