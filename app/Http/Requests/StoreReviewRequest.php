<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'rating'          => ['required','integer','min:1','max:5'],
            'comment'         => ['required','string'],
            'product_id'      => ['nullable','exists:products,products_id','required_without:user_product_id','prohibited_with:user_product_id'],
            'user_product_id' => ['nullable','exists:user_products,user_product_id','required_without:product_id','prohibited_with:product_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required_without'      => 'Debes reseñar un producto del admin o uno de usuarios.',
            'user_product_id.required_without' => 'Debes reseñar un producto del admin o uno de usuarios.',
            'product_id.prohibited_with'       => 'Solo uno: product_id o user_product_id.',
            'user_product_id.prohibited_with'  => 'Solo uno: product_id o user_product_id.',
        ];
    }
}
