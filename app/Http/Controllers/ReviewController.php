<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\FacebookReview;

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
