<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CartItemsController extends Controller
{
    public function index(){

        $user = Auth::user(); //variable que guarda el usuario autenticado
        $userID = $user->user_id; //variable que guarda el id del usuario autenticado
        $cart = Cart::firstOrCreate(['user_id' => $userID]); //busca el carrito del usuario autenticado
        $CartItems = CartItem::where('cart_id', $cart->cart_id)->get(); //busca los items del carrito del usuario autenticado

        return view('users.cartItems', compact('CartItems', 'userID', 'cart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,products_id',
            'count' => 'required|numeric|min:1',
        ]);

        $userID = Auth::id();
        if (!$userID) {
            return response()->json(['success' => false, 'message' => 'Debes iniciar sesión.'], 401);
        }

        $cart = Cart::firstOrCreate(['user_id' => $userID]);
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'El producto no existe.'], 404);
        }

        $unitPrice = $product->price;

        $existingCartItem = CartItem::where('cart_id', $cart->cart_id)
                                    ->where('products_id', $product->products_id)
                                    ->first();

        if ($existingCartItem) {
            $existingCartItem->count += $request->count;
            $existingCartItem->unit_price = $unitPrice;
            $existingCartItem->save();
            $message = 'Cantidad del producto actualizada en el carrito.';
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->cart_id,
                'products_id' => $product->products_id,
                'count' => $request->count,
                'unit_price' => $unitPrice,
            ]);
            $message = 'Producto agregado al carrito.';
        }

        // Calcula el nuevo total de ítems en el carrito (o la suma de las cantidades)
        $newCartItemCount = CartItem::where('cart_id', $cart->cart_id)->sum('count');

        return response()->json([
            'success' => true,
            'message' => $message,
            'newCartItemCount' => $newCartItemCount
        ]);
    }

    public function updateQuantity(Request $request, CartItem $cartItem){
        // Cambiado 'action' por 'operation'
        $request->validate([
            'operation' => 'required|in:increase,decrease',
        ]);

        if(Auth::id() != $cartItem->cart->user_id){
            // Si el usuario no tiene permiso, devolver JSON
            return response()->json(['success' => false, 'message' => 'No tienes permiso para modificar este item.'], 403);
        }

        // Cambiado $request->action por $request->operation
        if($request->operation === 'increase'){
            $cartItem->count++;
        } elseif ($request->operation === 'decrease'){
            $cartItem->count--;
        }

        if($cartItem->count <= 0){
            $cartItem->delete();
            // Devuelve JSON indicando que el ítem fue eliminado
            $cart = $cartItem->cart; // Obtener el carrito asociado
            $subtotalGeneral = CartItem::where('cart_id', $cart->cart_id)->get()->sum(function($item) {
                return $item->count * $item->unit_price;
            });
            $totalProductsCount = CartItem::where('cart_id', $cart->cart_id)->sum('count');

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado del carrito.',
                'item_removed' => true, // Indicador para JavaScript
                'itemId' => $cartItem->id_cart_items, // Para saber qué elemento HTML eliminar
                'newSubtotalGeneral' => number_format($subtotalGeneral, 2, '.', ''),
                'newTotalProductsCount' => $totalProductsCount,
            ]);
        }

        $cartItem->save();

        // Calcula los nuevos subtotales y la cantidad total de artículos para actualizar la vista
        $subtotalItem = $cartItem->count * $cartItem->unit_price;
        $cart = $cartItem->cart; // Obtener el carrito asociado
        $subtotalGeneral = CartItem::where('cart_id', $cart->cart_id)->get()->sum(function($item) {
            return $item->count * $item->unit_price;
        });
        $totalProductsCount = CartItem::where('cart_id', $cart->cart_id)->sum('count');

        // Devuelve JSON con los datos actualizados
        return response()->json([
            'success' => true,
            'message' => 'Cantidad del producto actualizada en el carrito.',
            'newCount' => $cartItem->count,
            'newSubtotalItem' => number_format($subtotalItem, 2, '.', ''),
            'newSubtotalGeneral' => number_format($subtotalGeneral, 2, '.', ''),
            'newTotalProductsCount' => $totalProductsCount,
        ]);
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->load('cart');
        if (Auth::id() != $cartItem->cart->user_id) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar este ítem.'], 403);
        }

        $itemId = $cartItem->id_cart_items; // Guarda el ID antes de eliminarlo
        $cart = $cartItem->cart; // Obtener el carrito asociado

        $cartItem->delete();

        // Recalcular los totales después de la eliminación
        $subtotalGeneral = CartItem::where('cart_id', $cart->cart_id)->get()->sum(function($item) {
            return $item->count * $item->unit_price;
        });
        $totalProductsCount = CartItem::where('cart_id', $cart->cart_id)->sum('count');

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito.',
            'item_removed' => true,
            'itemId' => $itemId, // ID del ítem eliminado para JS
            'newSubtotalGeneral' => number_format($subtotalGeneral, 2, '.', ''),
            'newTotalProductsCount' => $totalProductsCount,
            'cartEmpty' => CartItem::where('cart_id', $cart->cart_id)->doesntExist() // Para saber si el carrito quedó vacío
        ]);
    }

    public function deleteAll(){
        $userID = Auth::id();
        $cart = Cart::firstOrCreate(['user_id' => $userID]);

        CartItem::where('cart_id', $cart->cart_id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compra realizada con éxito.', // Mensaje más neutro si se va a redirigir
            'cart_cleared' => true, // Indicador para JS de que el carrito está vacío
            'redirect_url' => route('user.product') // URL a la que redirigir
        ]);
    }
}
