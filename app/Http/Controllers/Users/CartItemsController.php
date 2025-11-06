<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartItemsController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $userID = $user->user_id;

        $cart   = Cart::firstOrCreate(['user_id' => $userID]);
        $CartItems = CartItem::with(['product', 'userProduct'])
        ->where('cart_id', $cart->cart_id)
        ->get();
        return view('users.cartItems', compact('CartItems', 'userID', 'cart'));
    }

    public function store(Request $request)
    {
        // ✅ Soporta product_id (oficial) O user_product_id (segunda mano)
        $request->validate([
            'product_id'      => 'nullable|exists:products,products_id|required_without:user_product_id',
            'user_product_id' => 'nullable|exists:user_products,user_product_id|required_without:product_id',
            'count'           => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión.'
            ], 401);
        }

        $userID = $user->user_id;
        $cart   = Cart::firstOrCreate(['user_id' => $userID]);

        $count = $request->count;
        $unitPrice = null;
        $message   = '';

        // 🔹 Caso 1: producto oficial
        if ($request->filled('product_id')) {
            $product = Product::find($request->product_id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no existe.'
                ], 404);
            }

            $unitPrice = $product->price;

            $existingCartItem = CartItem::where('cart_id', $cart->cart_id)
                ->where('products_id', $product->products_id)
                ->whereNull('user_product_id')
                ->first();

            if ($existingCartItem) {
                $existingCartItem->count      += $count;
                $existingCartItem->unit_price  = $unitPrice;
                $existingCartItem->save();
                $message = 'Cantidad del producto actualizada en el carrito.';
            } else {
                CartItem::create([
                    'cart_id'        => $cart->cart_id,
                    'products_id'    => $product->products_id,
                    'user_product_id'=> null,
                    'count'          => $count,
                    'unit_price'     => $unitPrice,
                ]);
                $message = 'Producto agregado al carrito.';
            }
        }

        // 🔹 Caso 2: pieza de usuario
        if ($request->filled('user_product_id')) {
            $userProduct = UserProduct::find($request->user_product_id);

            if (!$userProduct) {
                return response()->json([
                    'success' => false,
                    'message' => 'La pieza de usuario no existe.'
                ], 404);
            }

            $unitPrice = $userProduct->price;

            $existingCartItem = CartItem::where('cart_id', $cart->cart_id)
                ->where('user_product_id', $userProduct->user_product_id)
                ->whereNull('products_id')
                ->first();

            if ($existingCartItem) {
                $existingCartItem->count      += $count;
                $existingCartItem->unit_price  = $unitPrice;
                $existingCartItem->save();
                $message = 'Cantidad de la pieza actualizada en el carrito.';
            } else {
                CartItem::create([
                    'cart_id'        => $cart->cart_id,
                    'products_id'    => null,
                    'user_product_id'=> $userProduct->user_product_id,
                    'count'          => $count,
                    'unit_price'     => $unitPrice,
                ]);
                $message = 'Pieza agregada al carrito.';
            }
        }

        $newCartItemCount = CartItem::where('cart_id', $cart->cart_id)->sum('count');

        return response()->json([
            'success'          => true,
            'message'          => $message,
            'newCartItemCount' => $newCartItemCount,
        ]);
    }

    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'operation' => 'required|in:increase,decrease',
        ]);

        if (Auth::user()->user_id != $cartItem->cart->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar este item.'
            ], 403);
        }

        if ($request->operation === 'increase') {
            $cartItem->count++;
        } elseif ($request->operation === 'decrease') {
            $cartItem->count--;
        }

        if ($cartItem->count <= 0) {
            $cart = $cartItem->cart;
            $itemId = $cartItem->id_cart_items;
            $cartItem->delete();

            $items = CartItem::where('cart_id', $cart->cart_id)->get();
            $subtotalGeneral = 0;
            foreach ($items as $item) {
                $subtotalGeneral += $item->count * $item->unit_price;
            }
            $totalProductsCount = $items->sum('count');

            return response()->json([
                'success'               => true,
                'message'               => 'Item eliminado del carrito.',
                'item_removed'          => true,
                'itemId'                => $itemId,
                'newSubtotalGeneral'    => number_format($subtotalGeneral, 2, '.', ''),
                'newTotalProductsCount' => $totalProductsCount,
            ]);
        }

        $cartItem->save();

        $subtotalItem = $cartItem->count * $cartItem->unit_price;
        $cart = $cartItem->cart;

        $items = CartItem::where('cart_id', $cart->cart_id)->get();
        $subtotalGeneral = 0;
        foreach ($items as $item) {
            $subtotalGeneral += $item->count * $item->unit_price;
        }
        $totalProductsCount = $items->sum('count');

        return response()->json([
            'success'               => true,
            'message'               => 'Cantidad del producto actualizada en el carrito.',
            'newCount'              => $cartItem->count,
            'newSubtotalItem'       => number_format($subtotalItem, 2, '.', ''),
            'newSubtotalGeneral'    => number_format($subtotalGeneral, 2, '.', ''),
            'newTotalProductsCount' => $totalProductsCount,
        ]);
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->load('cart');

        if (Auth::user()->user_id != $cartItem->cart->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este ítem.'
            ], 403);
        }

        $itemId = $cartItem->id_cart_items;
        $cart   = $cartItem->cart;

        $cartItem->delete();

        $items = CartItem::where('cart_id', $cart->cart_id)->get();
        $subtotalGeneral = 0;
        foreach ($items as $item) {
            $subtotalGeneral += $item->count * $item->unit_price;
        }
        $totalProductsCount = $items->sum('count');
        $cartEmpty = $items->isEmpty();

        return response()->json([
            'success'               => true,
            'message'               => 'Producto eliminado del carrito.',
            'item_removed'          => true,
            'itemId'                => $itemId,
            'newSubtotalGeneral'    => number_format($subtotalGeneral, 2, '.', ''),
            'newTotalProductsCount' => $totalProductsCount,
            'cartEmpty'             => $cartEmpty,
        ]);
    }

    public function deleteAll(Request $request)
    {
        $user   = Auth::user();
        $userID = $user->user_id;

        $cart   = Cart::firstOrCreate(['user_id' => $userID]);
        $items  = CartItem::where('cart_id', $cart->cart_id)->get();

        if ($items->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tu carrito está vacío.',
                ], 422);
            }

            return back()->withErrors(['cart' => 'Tu carrito está vacío.']);
        }

        $orderId = DB::transaction(function () use ($items, $cart, $userID) {
            $total = 0;
            foreach ($items as $ci) {
                $total += $ci->count * $ci->unit_price;
            }

            $order = Order::create([
                'user_id' => $userID,
                'total'   => $total,
                'status'  => 'paid',
            ]);

            foreach ($items as $ci) {
                OrderItem::create([
                    'order_id'        => $order->order_id,
                    'product_id'      => $ci->products_id,      // puede ser null
                    'user_product_id' => $ci->user_product_id,  // puede ser null
                    'count'           => $ci->count,
                    'unit_price'      => $ci->unit_price,
                ]);
            }

            CartItem::where('cart_id', $cart->cart_id)->delete();

            return $order->order_id;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Compra realizada con éxito.',
                'cart_cleared' => true,
                'order_id'     => $orderId,
                'redirect_url' => route('index'),
            ]);
        }

        return redirect()
            ->route('index')
            ->with('success', '¡Compra realizada!');
    }
}
