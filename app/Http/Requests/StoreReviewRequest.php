<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\PurchasedItem;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ya protegemos la ruta con middleware auth, pero esto asegura
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // EXACTAMENTE uno de los dos: product_id o user_product_id
            'product_id' => [
                'nullable',
                'required_without:user_product_id',
                'prohibits:user_product_id',
                Rule::exists('products', 'products_id'),
                // Solo podrá pasar si el usuario compró este producto
                new PurchasedItem('product'),
            ],

            'user_product_id' => [
                'nullable',
                'required_without:product_id',
                'prohibits:product_id',
                Rule::exists('user_products', 'user_product_id'),
                // Solo podrá pasar si el usuario compró esta pieza de usuario
                new PurchasedItem('user_product'),
            ],

            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:5', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required_without'      => 'Debes enviar product_id o user_product_id.',
            'user_product_id.required_without' => 'Debes enviar user_product_id o product_id.',
            'product_id.prohibits'             => 'No puedes enviar product_id y user_product_id al mismo tiempo.',
            'user_product_id.prohibits'        => 'No puedes enviar user_product_id y product_id al mismo tiempo.',

            'rating.required'                  => 'La calificación es obligatoria.',
            'rating.between'                   => 'La calificación debe estar entre 1 y 5.',
            'comment.required'                 => 'Debes escribir un comentario.',
            'comment.min'                      => 'El comentario debe tener al menos :min caracteres.',
        ];
    }
}
