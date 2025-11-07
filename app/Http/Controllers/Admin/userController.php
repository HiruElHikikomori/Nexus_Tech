<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class userController extends Controller
{
    public function index(Request $request){

        $query = $request->input('query'); // Obtener el término de búsqueda

        $users = User::query(); // Iniciar una nueva consulta de usuarios

        // Filtrar por rol_id = 2
        $users->where('rol_id', 2);

        if ($query) {
            // Aplicar los filtros si hay una consulta
            $users->where(function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
                // Se pueden añadir más campos por los que buscar aquí
            });
        }

        // --- CAMBIO AQUÍ: Añadir appends() ---
        $users = $users->paginate(10)->appends(request()->query());

        $roles = Role::all();
        $carts = Cart::all();
        return view('admin.usersTable', compact('users', 'roles', 'carts'));
    }

    public function destroy(User $user) // Asumiendo Route Model Binding para User
    {
        // ================== NUEVO: eliminar piezas del usuario ==================
        // Recorremos todas las piezas publicadas por este usuario
        foreach ($user->userProducts as $userProduct) {

            // Borrar reportes asociados a esa pieza (si existe la relación)
            if (method_exists($userProduct, 'reports')) {
                $userProduct->reports()->delete();
            }

            // Borrar imagen física si no es la default
            if ($userProduct->img_name && $userProduct->img_name !== 'default.png') {
                $path = public_path('img/user_products/' . $userProduct->img_name);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }

            // Borrar la pieza
            $userProduct->delete();
        }
        // ================== FIN BLOQUE NUEVO ==================

        $userID = $user->user_id;
        $cart = Cart::firstOrCreate(['user_id' => $userID]);

        //elimina todos los items del carrito si es que hay
        CartItem::where('cart_id', $cart->cart_id)->delete();

        //Borra el carrito asignado para el usuario
        $cart->delete();
        //Banea al usuario
        $user->delete();

        return redirect()->route('admin.users.index')->with(
            'success',
            'Usuario y todos sus carritos, ítems de carrito y piezas de usuario eliminados con éxito.'
        );
    }
}
