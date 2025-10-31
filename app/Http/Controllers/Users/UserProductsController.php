<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserProductRequest;
use App\Http\Requests\UpdateUserProductRequest;
use App\Models\ProductType;
use App\Models\UserProduct;
use Illuminate\Http\Request;

class UserProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // requiere usuario autenticado
    }

    /**
     * GET /users/{userId}/piezas
     */
    public function index(Request $request, $userId)
    {
        // Asegura que el userId de la ruta sea el mismo que el autenticado
        abort_unless($request->user()->user_id == (int) $userId, 403);

        $items = UserProduct::where('user_id', $request->user()->user_id)
            ->orderByDesc('user_product_id')
            ->paginate(12);

        $productTypes = ProductType::all();

        // Enviamos routeUserId para construir rutas en Blade fácilmente
        return view('users.user_products.index', compact('items', 'productTypes'))
            ->with('routeUserId', $userId);
    }

    /**
     * POST /users/{userId}/piezas
     */
    public function store(StoreUserProductRequest $request, $userId)
    {
        abort_unless($request->user()->user_id == (int) $userId, 403);

        $this->authorize('create', UserProduct::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->user_id;

        // Manejo de imagen
        $filename = 'default.png';
        if ($request->hasFile('img_name')) {
            $file = $request->file('img_name');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/user_products'), $filename);
        }
        $data['img_name'] = $filename;

        UserProduct::create($data);

        return redirect()
            ->route('users.user_products.index', ['userId' => $userId])
            ->with('success', 'Tu pieza fue publicada.');
    }

    /**
     * PUT /users/{userId}/piezas/{userProduct}
     */
    public function update(UpdateUserProductRequest $request, $userId, UserProduct $userProduct)
    {
        abort_unless($request->user()->user_id == (int) $userId, 403);

        $this->authorize('update', $userProduct);

        $data = $request->validated();

        // Si sube nueva imagen, borrar la anterior (si no es default)
        if ($request->hasFile('img_name')) {
            if ($userProduct->img_name && $userProduct->img_name !== 'default.png') {
                $old = public_path('img/user_products/' . $userProduct->img_name);
                if (file_exists($old)) {
                    @unlink($old);
                }
            }
            $file = $request->file('img_name');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/user_products'), $filename);
            $data['img_name'] = $filename;
        } else {
            // Si no se sube nueva, mantener la actual (o default si no hay)
            $data['img_name'] = $userProduct->img_name ?? 'default.png';
        }

        $userProduct->update($data);

        return redirect()
            ->route('users.user_products.index', ['userId' => $userId])
            ->with('success', 'Pieza actualizada.');
    }

    /**
     * DELETE /users/{userId}/piezas/{userProduct}
     */
    public function destroy(Request $request, $userId, UserProduct $userProduct)
    {
        abort_unless($request->user()->user_id == (int) $userId, 403);

        $this->authorize('delete', $userProduct);

        // Borrar imagen física si no es la default
        if ($userProduct->img_name && $userProduct->img_name !== 'default.png') {
            $path = public_path('img/user_products/' . $userProduct->img_name);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $userProduct->delete();

        return redirect()
            ->route('users.user_products.index', ['userId' => $userId])
            ->with('success', 'Pieza eliminada.');
    }
}
