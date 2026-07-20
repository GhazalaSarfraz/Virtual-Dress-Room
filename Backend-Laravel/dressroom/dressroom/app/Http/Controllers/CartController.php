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
        $cartItems = Cart::with('product')->where('user_id', $userId)->where('status', 'Active')->get();
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
                    ->where('status', 'Active')
                    ->first();

        if ($cart) {
            $cart->quantity += $request->input('quantity', 1);
            $cart->save();
        } else {
            // Check if there is a removed one to restore, though creating new is also fine.
            $cart = Cart::create([
                'user_id' => $request->user_id,
                'product_id' => $request->product_id,
                'quantity' => $request->input('quantity', 1),
                'status' => 'Active'
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Added to cart', 'cart' => $cart]);
    }

    // ✅ NEW: Update quantity
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::find($id);
        if ($cart) {
            $cart->quantity = $request->quantity;
            $cart->save();
            return response()->json(['status' => 'success', 'message' => 'Cart updated', 'cart' => $cart]);
        }
        return response()->json(['status' => 'error', 'message' => 'Cart item not found'], 404);
    }

    public function destroy($id)
    {
        $cart = Cart::find($id);
        if ($cart) {
            $cart->status = 'Removed';
            $cart->save();
            return response()->json(['status' => 'success', 'message' => 'Removed from cart']);
        }
        return response()->json(['status' => 'error', 'message' => 'Cart item not found'], 404);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'checkout_mode' => 'nullable|string|in:cart,buy_now',
            'selected_cart_ids' => 'nullable|array',
            'buy_now_product_id' => 'nullable|exists:products,id',
            'buy_now_quantity' => 'nullable|integer|min:1'
        ]);

        $mode = $request->input('checkout_mode');
        
        // Robust fallback: if mode is missing but buy_now_product_id is provided, assume buy_now
        if (!$mode && $request->has('buy_now_product_id') && $request->buy_now_product_id) {
            $mode = 'buy_now';
        } elseif (!$mode) {
            $mode = 'cart';
        }

        $totalAmount = 0;
        $orderItems = [];
        $cartsToUpdate = collect();

        // Debug log (remove later)
        \Log::info('Checkout Attempt', [
            'mode' => $mode,
            'input' => $request->all()
        ]);

        if ($mode === 'buy_now') {
            $product = \App\Models\Product::findOrFail($request->buy_now_product_id);
            $quantity = $request->input('buy_now_quantity', 1);
            $totalAmount = $product->price * $quantity;
            
            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price
            ];
        } else {
            // Cart Mode
            $query = Cart::with('product')->where('user_id', $request->user_id)->where('status', 'Active');
            
            $selectedIds = $request->selected_cart_ids;
            if (is_string($selectedIds)) {
                $selectedIds = json_decode($selectedIds, true);
            }
            
            if (is_array($selectedIds) && count($selectedIds) > 0) {
                $query->whereIn('id', $selectedIds);
            }
            
            $carts = $query->get();
            
            if ($carts->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'No items selected or cart is empty'], 400);
            }
            
            foreach ($carts as $cart) {
                if ($cart->product) {
                    $totalAmount += $cart->product->price * $cart->quantity;
                    $orderItems[] = [
                        'product_id' => $cart->product_id,
                        'quantity' => $cart->quantity,
                        'price' => $cart->product->price
                    ];
                }
            }
            $cartsToUpdate = $carts;
        }

        // Add delivery fee if applicable
        if ($totalAmount > 0) {
            $totalAmount += 5.00; // Adding delivery fee just like frontend calculation
        }

        // Create Order
        $order = \App\Models\Order::create([
            'user_id' => $request->user_id,
            'total_amount' => $totalAmount,
            'payment_status' => 'Paid',
            'order_status' => 'Processing',
            'shipping_address' => 'Checkout via App'
        ]);

        // Create Order Items
        foreach ($orderItems as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }
        
        // Update Cart Status
        foreach ($cartsToUpdate as $cart) {
            $cart->status = 'Ordered';
            $cart->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Checked out successfully', 'order_id' => $order->id]);
    }
}