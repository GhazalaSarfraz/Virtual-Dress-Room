<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    $setting = SiteSetting::firstOrCreate([], [
        'site_name' => 'Virtual Dress Room',
        'tagline' => 'Virtual Try-on & Fitting Suite',
        'welcome_title' => 'The future of fitting rooms.',
        'welcome_description' => 'Browse products and try them on virtually using our advanced AI technology.',
        'editorial_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBBSSEpiXwE_TWupkDKt4WLQdDffVq7Vivn6pVSV6xY5aohPmYc-2zoYfzGrH-T1w1L0LKOrAF19XJD33vOnAl4jzxPM7V1hGRJRJsVuWHtad_OAFHBJuhJS2eMH7VWLc_AHyRVvG8BV1Rq1Vi94jQZCTwfGStMvzsmR8XrDVlw_ka6NJ6wDoaC4cNS72n_MPk1GsJ17h6vzcgEfVHv99_nCbeRzizc1iCxBgnRPIyAGwGwVa_RC1i0KPrLB1t0AlawXeHFMda840oL',
        'editorial_small_text' => 'AI-DRIVEN COUTURE',
        'editorial_heading' => 'The future of fitting rooms.'
    ]);
    return view('welcome', compact('setting'));
});

Route::get('/login', function () {
    if (Auth::check()) {
        return strtolower(Auth::user()->role) === 'admin' ? redirect('/admin/dashboard') : redirect('/user/dashboard');
    }
    $setting = SiteSetting::firstOrCreate([], [
        'site_name' => 'Virtual Dress Room'
    ]);
    return view('login', compact('setting'));
})->name('login');

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'role' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        if (strtolower($user->role) !== strtolower($request->role)) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Access Denied: You are not registered as ' . $request->role,
            ]);
        }

        if (strtolower($user->role) === 'admin') {
            return redirect('/admin/dashboard');
        }
        if ($request->filled('redirect')) {
            return redirect($request->redirect);
        }
        return redirect('/user/dashboard');
    }

    return back()->withErrors([
        'email' => 'Invalid email or password',
    ]);
});

Route::get('/register', function () {
    if (Auth::check()) {
        return strtolower(Auth::user()->role) === 'admin' ? redirect('/admin/dashboard') : redirect('/user/dashboard');
    }
    $setting = SiteSetting::firstOrCreate([], [
        'site_name' => 'Virtual Dress Room'
    ]);
    return view('register', compact('setting'));
})->name('register');

Route::post('/register', function (Request $request) {
    $request->validate([
        'username' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'phone' => 'nullable|string|max:255',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|string',
    ]);

    $user = \App\Models\User::create([
        'username' => $request->username,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'role' => strtolower($request->role) ?? 'user',
    ]);

    Auth::login($user);

    if (strtolower($user->role) === 'admin') {
        return redirect('/admin/dashboard');
    }
    if ($request->filled('redirect')) {
        return redirect($request->redirect);
    }
    return redirect('/user/dashboard');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/clear-cache', function() {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return "All Caches Cleared! Ab try karein.";
});

Route::get('/forgot-password', function () {
    $setting = SiteSetting::firstOrCreate([], [
        'site_name' => 'Virtual Dress Room'
    ]);
    return view('forgot-password', compact('setting'));
})->name('password.request');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    
    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('/reset-password/{token}', function (Request $request, $token) {
    $setting = SiteSetting::firstOrCreate([], ['site_name' => 'Virtual Dress Room']);
    return view('reset-password', ['token' => $token, 'email' => $request->email, 'setting' => $setting]);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (\App\Models\User $user, string $password) {
        $user->forceFill([
            'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));

        $user->save();
        event(new PasswordReset($user));
    });

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->name('password.update');

Route::get('/user/dashboard', function () {
    if (Auth::check()) {
        $role = strtolower(Auth::user()->role);
        if ($role === 'admin') {
            return redirect('/admin/dashboard');
        }
        if ($role !== 'user') {
            Auth::logout();
            return redirect('/login');
        }
        $orders = \App\Models\Order::with('items.product')->where('user_id', Auth::id())->latest()->get();
        $tryOnHistories = \App\Models\TryOnHistory::with('product')->where('user_id', Auth::id())->latest()->get();
        $userReviews = \App\Models\Review::where('user_id', Auth::id())->get()->keyBy('product_id');
    } else {
        $orders = collect();
        $tryOnHistories = collect();
        $userReviews = collect();
    }
    
    $products = Product::withCount('reviews')->withAvg('reviews', 'rating')->get();
    $categories = Product::select('category')->distinct()->pluck('category')->filter();
    $setting = SiteSetting::firstOrCreate([], [
        'site_name' => 'Virtual Dress Room',
        'tagline' => 'Virtual Try-on & Fitting Suite',
        'welcome_title' => 'The future of fitting rooms.',
        'welcome_description' => 'Browse products and try them on virtually using our advanced AI technology.',
        'editorial_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBBSSEpiXwE_TWupkDKt4WLQdDffVq7Vivn6pVSV6xY5aohPmYc-2zoYfzGrH-T1w1L0LKOrAF19XJD33vOnAl4jzxPM7V1hGRJRJsVuWHtad_OAFHBJuhJS2eMH7VWLc_AHyRVvG8BV1Rq1Vi94jQZCTwfGStMvzsmR8XrDVlw_ka6NJ6wDoaC4cNS72n_MPk1GsJ17h6vzcgEfVHv99_nCbeRzizc1iCxBgnRPIyAGwGwVa_RC1i0KPrLB1t0AlawXeHFMda840oL',
        'editorial_small_text' => 'AI-DRIVEN COUTURE',
        'editorial_heading' => 'The future of fitting rooms.'
    ]);
    
    return view('user.dashboard', compact('products', 'categories', 'setting', 'orders', 'tryOnHistories', 'userReviews'));
})->name('user.dashboard');

Route::get('/try-on-image/{filename}', function ($filename) {
    // Check multiple possible storage locations
    $possiblePaths = [
        'public/try_ons/' . $filename,
        'public/results/' . $filename,
    ];
    $foundPath = null;
    foreach ($possiblePaths as $path) {
        if (\Illuminate\Support\Facades\Storage::exists($path)) {
            $foundPath = $path;
            break;
        }
    }
    if (!$foundPath) {
        abort(404);
    }
    $file = \Illuminate\Support\Facades\Storage::get($foundPath);
    $type = \Illuminate\Support\Facades\Storage::mimeType($foundPath);
    return response($file, 200)->header('Content-Type', $type);
});

Route::get('/product/{id}/reviews', function ($id) {
    $reviews = \App\Models\Review::with('user')->where('product_id', $id)->latest()->get();
    $hasPurchased = false;
    if (Auth::check()) {
        $hasPurchased = \App\Models\Order::where('user_id', Auth::id())
            ->whereHas('items', function($q) use ($id) {
                $q->where('product_id', $id);
            })->exists();
    }
    return response()->json(['status' => 'success', 'reviews' => $reviews, 'has_purchased' => $hasPurchased]);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {

    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::post('/admin/settings', function (Request $request) {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'site_name' => 'required|string',
            'tagline' => 'nullable|string',
            'welcome_title' => 'nullable|string',
            'welcome_description' => 'nullable|string',
            'editorial_small_text' => 'nullable|string',
            'editorial_heading' => 'nullable|string',
            'editorial_image_file' => 'nullable|image|max:10240',
            'editorial_image_url' => 'nullable|string',
        ]);

        $setting = SiteSetting::firstOrCreate([]);
        $data = $request->only([
            'site_name', 'tagline', 'welcome_title', 'welcome_description', 'editorial_small_text', 'editorial_heading'
        ]);

        if ($request->hasFile('editorial_image_file')) {
            $path = $request->file('editorial_image_file')->store('settings', 'public');
            $data['editorial_image'] = '/storage/' . $path;
        } elseif ($request->editorial_image_url) {
            $data['editorial_image'] = $request->editorial_image_url;
        }

        $setting->update($data);
        return back()->with('success', 'Site settings updated successfully');
    })->name('admin.settings.update');

    Route::post('/admin/orders/{id}/status', function (Request $request, $id) {
        if (strtolower(Auth::user()->role) !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'status' => 'required|in:Processing,Shipped,Delivered,Cancelled'
        ]);
        $order = \App\Models\Order::findOrFail($id);
        $order->order_status = $request->status;
        $order->save();
        return response()->json(['status' => 'success', 'message' => 'Order status updated', 'order_status' => $order->order_status]);
    })->name('admin.orders.status');

    Route::get('/user/fitting-room', function () {
        if (strtolower(Auth::user()->role) !== 'user') {
            return redirect('/admin/dashboard');
        }
        $products = Product::all();
        $setting = SiteSetting::firstOrCreate([]);
        return view('fitting-room', compact('products', 'setting'));
    })->name('user.fitting-room');

    Route::delete('/user/try-on-history/{id}', function ($id) {
        $history = \App\Models\TryOnHistory::where('user_id', Auth::id())->findOrFail($id);
        try {
            if ($history->result_image_url) {
                $path = str_replace('/storage/', 'public/', $history->result_image_url);
                \Illuminate\Support\Facades\Storage::delete($path);
            }
            if ($history->human_image_url) {
                $path = str_replace('/storage/', 'public/', $history->human_image_url);
                \Illuminate\Support\Facades\Storage::delete($path);
            }
        } catch (\Exception $e) {
            // Ignore storage errors
        }
        $history->delete();
        return response()->json(['status' => 'success', 'message' => 'History item deleted successfully']);
    })->name('user.tryon.delete');

    Route::post('/user/reviews', function (Request $request) {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        
        $hasPurchased = \App\Models\Order::where('user_id', Auth::id())
            ->whereHas('items', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })->exists();
            
        if (!$hasPurchased) {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'You must purchase this product to leave a review.'], 403);
            }
            return back()->with('error', 'You must purchase this product to leave a review.');
        }

        $ratingText = match((int)$request->rating) {
            5 => 'Excellent',
            4 => 'Good',
            3 => 'Average',
            2 => 'Poor',
            1 => 'Terrible',
            default => ''
        };

        $review = \App\Models\Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ],
            [
                'rating' => $request->rating,
                'comment' => $ratingText
            ]
        );
        if ($request->expectsJson()) {
            return response()->json(['status' => 'success', 'message' => 'Review submitted successfully', 'review' => $review]);
        }
        return back()->with('success', 'Thank you! Your review has been submitted.');
    })->name('user.reviews.store');

    Route::delete('/admin/reviews/{id}', function ($id) {
        if (strtolower(Auth::user()->role) !== 'admin') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }
        $review = \App\Models\Review::findOrFail($id);
        $review->delete();
        return response()->json(['status' => 'success', 'message' => 'Review deleted successfully']);
    })->name('admin.reviews.delete');
});