<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Order;
use App\Models\TryOnHistory;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect('/user/dashboard');
        }

        // Analytics
        $totalUsers = User::where('id', '!=', auth()->id())->count();
        $totalOrders = Order::count();
        
        $topWishlisted = Product::withCount('wishlists')
                            ->orderBy('wishlists_count', 'desc')
                            ->take(5)
                            ->get();
                            
        $topCartItems = Product::withCount('carts')
                            ->orderBy('carts_count', 'desc')
                            ->take(5)
                            ->get();

        // Data for Tabs
        $users = User::where('id', '!=', auth()->id())->get();
        $products = Product::all();
        $categories = Product::select('category')->distinct()->pluck('category')->filter();
        $orders = Order::with('user', 'items.product')->latest()->get();
        $tryOnHistories = TryOnHistory::with('user', 'product')->latest()->get();
        $wishlists = Wishlist::with('user', 'product')->latest()->get();
        $carts = Cart::with('user', 'product')->latest()->get();
        $reviews = \App\Models\Review::with('user', 'product')->latest()->get();

        $setting = \App\Models\SiteSetting::first() ?? new \App\Models\SiteSetting();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalOrders', 'topWishlisted', 'topCartItems',
            'users', 'products', 'categories', 'orders', 'tryOnHistories', 
            'wishlists', 'carts', 'setting', 'reviews'
        ));
    }
}
