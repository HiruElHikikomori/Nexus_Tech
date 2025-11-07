<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Http\Request;

class UserProductsAdminController extends Controller
{
    public function __construct()
    {
        // Solo admins pueden entrar a estas rutas
        $this->middleware(['auth', 'role:Administrador']);
    }

    public function listByUser(User $user)
    {
        $items = UserProduct::with(['type', 'owner'])
            ->where('user_id', $user->user_id)
            ->orderByDesc('user_product_id')
            ->paginate(20);

        return view('admin.user_products.index', compact('user', 'items'));
    }

    public function destroy(UserProduct $userProduct)
    {
        // Borrar imagen física si no es la default
        if ($userProduct->img_name && $userProduct->img_name !== 'default.png') {
            $path = public_path('img/user_products/' . $userProduct->img_name);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $userProduct->delete();

        return back()->with('success', 'Pieza eliminada por moderación.');
    }
}
