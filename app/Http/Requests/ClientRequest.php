<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientRequest extends FormRequest
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
        $clientId = $this->route('client');
        return [
            'nome_client' => 'required',
            'fone_client' => 'required',
            'type_client' => 'required',
            'type_partner' => 'required',
            'cpf_cnpj_client' => 'required|unique:clients,cpf_cnpj_client,'. ($clientId ? $clientId->id : null),

        ];
    }

    public function messages(): array
    {

        return [
            'nome_client.required' => 'Campo nome é obrigatorio',
            'fone_client.required' => 'Campo fone é obrigatorio',
            'type_client.required' => 'Campo tipo é obrigatorio',
            'type_partner.required' => 'Campo tipo de pareciro é obrigatorio',
            'cpf_cnpj_client.required' => 'Campo CPF/CNPJ é obrigatorio',
            'cpf_cnpj_client.unique' => 'CPF/CNPJ já em uso',
        ];
    }
}
