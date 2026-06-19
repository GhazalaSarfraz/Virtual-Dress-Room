<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('products', ProductController::class);
Route::apiResource('cart', CartController::class)->only(['index', 'store', 'destroy']);
Route::apiResource('wishlist', WishlistController::class)->only(['index', 'store', 'destroy']);

Route::post('/tryon', function (Request $request) {
    try {
        set_time_limit(0);
        
        $humanImageBase64 = $request->human_image;
        $clothImageUrl = $request->cloth_image_url;
        $description = $request->description ?? "beautiful dress";

        if (!$humanImageBase64 || !$clothImageUrl) {
            return response()->json(['status' => 'error', 'message' => 'Missing human image or cloth image URL']);
        }

        if (str_contains($clothImageUrl, '/storage/')) {
            $relativePath = explode('/storage/', $clothImageUrl)[1];
            $localFilePath = storage_path('app/public/' . $relativePath);
            $clothImageBytes = @file_get_contents($localFilePath);
            
            if (!$clothImageBytes) {
                $localFilePath = public_path('storage/' . $relativePath);
                $clothImageBytes = @file_get_contents($localFilePath);
            }
            
            if (!$clothImageBytes) {
                return response()->json(['status' => 'error', 'message' => 'Failed to read local cloth image from disk: ' . $localFilePath]);
            }
        } else {
            $clothImageBytes = @file_get_contents($clothImageUrl);
            if (!$clothImageBytes) {
                return response()->json(['status' => 'error', 'message' => 'Failed to download external cloth image: ' . $clothImageUrl]);
            }
        }

        $clothImageBase64 = base64_encode($clothImageBytes);

        // 👇 Hugging Face Space API URL 👇
        $huggingFaceUrl = 'https://ghazala-virtualtryon-my-virtual-tryon.hf.space/tryon';
        
        // Send request to the Hugging Face Space container API
        $response = Http::timeout(180)->post($huggingFaceUrl, [
            'human_image' => $humanImageBase64,
            'cloth_image' => $clothImageBase64,
            'description' => $description
        ]);

        if ($response->successful()) {
            // Forward the JSON response straight to Flutter
            $data = $response->json();
            return response()->json($data);
        } else {
            return response()->json([
                'status' => 'error', 
                'message' => 'Hugging Face Space API Error: ' . $response->body()
            ]);
        }
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Laravel Proxy Error: ' . $e->getMessage()
        ]);
    }
});