<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason'          => ['required', 'string', 'min:5'],
            'product_id'      => ['nullable', 'exists:products,products_id'],
            'user_product_id' => ['nullable', 'exists:user_products,user_product_id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasProductId = $this->filled('product_id');
            $hasUserProductId = $this->filled('user_product_id');

            // Si no hay ninguno → error
            if (!$hasProductId && !$hasUserProductId) {
                $validator->errors()->add('product_id', 'Debes reportar un producto del admin o uno de usuarios.');
            }

            // Si hay ambos → error (solo se permite uno)
            if ($hasProductId && $hasUserProductId) {
                $validator->errors()->add('product_id', 'Solo uno: product_id o user_product_id.');
                $validator->errors()->add('user_product_id', 'Solo uno: product_id o user_product_id.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Describe la razón del reporte.',
            'reason.min' => 'La descripción del motivo debe tener al menos 5 caracteres.',
            'product_id.exists' => 'El producto especificado no existe.',
            'user_product_id.exists' => 'La pieza de usuario especificada no existe.',
        ];
    }
}
