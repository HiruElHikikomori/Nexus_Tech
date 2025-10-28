<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'reason'          => ['required','string','min:5'],
            'product_id'      => ['nullable','exists:products,products_id','required_without:user_product_id','prohibited_with:user_product_id'],
            'user_product_id' => ['nullable','exists:user_products,user_product_id','required_without:product_id','prohibited_with:product_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required'                  => 'Describe la razón del reporte.',
            'product_id.required_without'      => 'Debes reportar un producto del admin o uno de usuarios.',
            'user_product_id.required_without' => 'Debes reportar un producto del admin o uno de usuarios.',
            'product_id.prohibited_with'       => 'Solo uno: product_id o user_product_id.',
            'user_product_id.prohibited_with'  => 'Solo uno: product_id o user_product_id.',
        ];
    }
}
