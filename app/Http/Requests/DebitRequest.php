<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DebitRequest extends FormRequest
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
        return [
            'name_debit' => 'required',
            'value_total_debit' => 'required',
            'forms_payments_id' => 'required',
            'banck_id' => 'required',
        ];
    }
    public function messages(): array
    {
        return [
            'name_debit.required' => 'Nome do debito é obrigatorio',
            'value_total_debit.required' => 'Valor é obrigatorio',
            'forms_payments_id.required' => 'Forma de pagamento é obrigatorio',
            'banck_id.required' => 'Banco é obrigatorio',
        ];
    }
}
