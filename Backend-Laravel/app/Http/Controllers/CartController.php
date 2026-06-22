<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        if (!$userId) {
            return response()->json(['status' => 'error', 'message' => 'user_id is required']);
        }
        
        $cartItems = Cart::with('product')->where('user_id', $userId)->get();
        return response()->json(['status' => 'success', 'cart' => $cartItems]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $cart = Cart::where('user_id', $request->user_id)
                    ->where('product_id', $request->product_id)
                    ->first();

        if ($cart) {
            $cart->quantity += $request->input('quantity', 1);
            $cart->save();
        } else {
            $cart = Cart::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'quantity' => $request->input('quantity', 1)
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Added to cart', 'cart' => $cart]);
    }

    public function destroy($id)
    {
        $cart = Cart::find($id);
        if ($cart) {
            $cart->delete();
            return response()->json(['status' => 'success', 'message' => 'Removed from cart']);
        }
        return response()->json(['status' => 'error', 'message' => 'Cart item not found'], 404);
    }
}
