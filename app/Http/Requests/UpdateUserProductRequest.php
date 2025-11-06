<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProductRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado a hacer esta petición.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $routeUserId = (int) $this->route('userId');
        $userProduct = $this->route('userProduct'); // gracias al route model binding

        if (!$user || !$userProduct) {
            return false;
        }

        // El usuario debe coincidir con la ruta Y ser dueño de la pieza
        return (int) $user->user_id === $routeUserId
            && (int) $userProduct->user_id === $routeUserId;
    }

    /**
     * Reglas de validación para ACTUALIZAR una pieza.
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
            // Imagen opcional al editar
            'img_name'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }
}
