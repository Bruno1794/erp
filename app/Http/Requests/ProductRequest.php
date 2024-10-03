<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userid = $this->route('product');
        //dd( $userid->cod_ncm);
        $userLogado = Auth::user();
        return [
            'name_product' => 'required_if:name_product,!=,null|',
             'barcode' => 'unique:products,barcode,' . ($userid ? $userid->id : ',id,enterprise_id, ' . $userLogado->enterprise_id),
        ];
    }
    public function messages(): array
    {
        return [
            'name_product.required_if' => 'Nome do produto e obrigatorio',
            'barcode.unique' => "Codigo de barras ja existe"
        ];
    }
}
