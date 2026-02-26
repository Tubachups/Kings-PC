<?php

use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;

$payload = [
    'budget' => 55000,
    'prompt' => 'Gaming + streaming build with strong 1440p performance and upgrade path.',
];

$request = Request::create('/shop/generate', 'POST', $payload);

$controller = app(ShopController::class);
$response = $controller->generate($request);

$status = $response->getStatusCode();
$data = json_decode($response->getContent(), true);

dump('HTTP Status', $status);
dump('Response', $data);
