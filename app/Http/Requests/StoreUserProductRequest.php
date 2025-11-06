<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserProductRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta petición.
     */
    public function authorize(): bool
    {
        $user = $this->user(); // usuario autenticado
        $routeUserId = (int) $this->route('userId'); // viene de /users/{userId}/piezas

        // Solo dejo pasar si está logueado y el userId de la ruta es el suyo
        return $user && (int) $user->user_id === $routeUserId;
        // Si quieres algo más relajado, puedes simplemente hacer: return $user !== null;
    }

    /**
     * Reglas de validación para crear una pieza de usuario.
     */
    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'product_type_id' => ['required', 'integer', 'exists:product_types,product_type_id'],
            'description'     => ['required', 'string'],
            'price'           => ['required', 'numeric', 'min:0'],
            'stock'           => ['required', 'integer', 'min:0'],
            'condition'       => ['nullable', 'string', 'max:50'],
            'img_name'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }
}
