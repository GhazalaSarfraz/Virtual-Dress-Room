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
Route::apiResource('cart', CartController::class)->only(['index', 'store', 'update', 'destroy']);
Route::post('/cart/checkout', [CartController::class, 'checkout']);
Route::apiResource('wishlist', WishlistController::class)->only(['index', 'store', 'destroy']);

Route::post('/tryon', function (Request $request) {
    try {
        set_time_limit(0);
        
        $humanImageBase64 = $request->human_image;
        $clothImageUrl    = $request->cloth_image_url;
        $description      = $request->description ?? "beautiful dress";

        if (!$humanImageBase64 || !$clothImageUrl) {
            return response()->json(['status' => 'error', 'message' => 'Missing human image or cloth image URL']);
        }

        // ── Decode human image (base64 → binary) ──────────────────────────
        $humanImageBytes = base64_decode($humanImageBase64);

        // ── Get cloth image bytes ──────────────────────────────────────────
        if (str_contains($clothImageUrl, '/storage/')) {
            $relativePath    = explode('/storage/', $clothImageUrl)[1];
            $localFilePath   = storage_path('app/public/' . $relativePath);
            $clothImageBytes = @file_get_contents($localFilePath);
            if (!$clothImageBytes) {
                $localFilePath   = public_path('storage/' . $relativePath);
                $clothImageBytes = @file_get_contents($localFilePath);
            }
            if (!$clothImageBytes) {
                return response()->json(['status' => 'error', 'message' => 'Failed to read cloth image: ' . $localFilePath]);
            }
        } else {
            $clothImageBytes = @file_get_contents($clothImageUrl);
            if (!$clothImageBytes) {
                return response()->json(['status' => 'error', 'message' => 'Failed to download cloth image: ' . $clothImageUrl]);
            }
        }

        // ── VirtualFit AI API URL (RunPod) — set VIRTUALFIT_API_URL in .env ──
        $virtualFitUrl = env('VIRTUALFIT_API_URL', 'https://ghazala-virtualtryon-my-virtual-tryon.hf.space/tryon');

        // ── Send multipart form-data to RunPod FastAPI server ─────────────
        $response = Http::timeout(180)
            ->attach('human_image',   $humanImageBytes,  'human.png',   ['Content-Type' => 'image/png'])
            ->attach('garment_image', $clothImageBytes,  'garment.png', ['Content-Type' => 'image/png'])
            ->post($virtualFitUrl, [
                'description'    => $description,
                'denoise_steps'  => 30,
                'seed'           => 42,
            ]);

        if ($response->successful()) {
            $data = $response->json();

            // ── Extract result image (server.py returns 'result_image' key) ──
            $resultSrc = '';
            if (isset($data['result_image']))      $resultSrc = $data['result_image'];
            elseif (isset($data['image']))         $resultSrc = $data['image'];
            elseif (isset($data['result']))        $resultSrc = $data['result'];
            elseif (is_array($data) && count($data) > 0) {
                $firstVal = reset($data);
                if (is_string($firstVal)) $resultSrc = $firstVal;
            }

            if (!$resultSrc) {
                return response()->json($data);
            }

            // ── Clean base64 data ─────────────────────────────────────────
            $base64Data    = preg_replace('#^data:image/\w+;base64,#i', '', $resultSrc);
            $imageContents = base64_decode($base64Data);

            // ── Save result image to local storage ────────────────────────
            $imageName = 'tryon_result_' . time() . '_' . Str::random(10) . '.png';
            $imagePath = 'public/try_ons/' . $imageName;

            if ($imageContents) {
                Storage::put($imagePath, $imageContents);
                $localImageUrl = '/storage/try_ons/' . $imageName;

                // Save TryOnHistory record
                if ($request->has('user_id')) {
                    $humanImageName = 'tryon_human_' . time() . '_' . Str::random(10) . '.png';
                    Storage::put('public/try_ons/' . $humanImageName, $humanImageBytes);

                    \App\Models\TryOnHistory::create([
                        'user_id'          => $request->user_id,
                        'product_id'       => $request->product_id,
                        'human_image_url'  => '/storage/try_ons/' . $humanImageName,
                        'result_image_url' => $localImageUrl,
                        'ai_prompt_used'   => $description,
                    ]);
                }

                return response()->json([
                    'status'     => 'success',
                    'image'      => url($localImageUrl),        // Full URL for web
                    'image_b64'  => 'data:image/png;base64,' . $base64Data, // base64 for Flutter
                ]);
            }

            // Fallback: return base64 directly if storage failed
            return response()->json([
                'status'    => 'success',
                'image'     => 'data:image/png;base64,' . $base64Data,
                'image_b64' => 'data:image/png;base64,' . $base64Data,
            ]);

        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'VirtualFit AI API Error: ' . $response->body()
            ]);
        }
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'VirtualFit AI Proxy Error: ' . $e->getMessage()
        ]);
    }
});