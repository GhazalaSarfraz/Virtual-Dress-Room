<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required']);
        }
        
        $wishlistItems = Wishlist::with('product')->where('user_id', $userId)->get();
        return response()->json(['status' => 'success', 'wishlist' => $wishlistItems]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id'
        ]);

        $exists = Wishlist::where('user_id', $request->user_id)
                          ->where('product_id', $request->product_id)
                          ->exists();

        if ($exists) {
            return response()->json(['status' => 'error', 'message' => 'Already in wishlist']);
        }

        $wishlist = Wishlist::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id
        ]);

        return response()->json(['status' => 'success', 'message' => 'Added to wishlist', 'wishlist' => $wishlist]);
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::find($id);
        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['status' => 'success', 'message' => 'Removed from wishlist']);
        }
        return response()->json(['status' => 'error', 'message' => 'Wishlist item not found'], 404);
    }
}
