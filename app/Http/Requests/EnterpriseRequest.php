<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EnterpriseRequest extends FormRequest
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
        $enterpriseId = $this->route('enterprise');

        return [
            //
            'name_enterprise' => 'required|string',
            'cpf_cnpj_enterprise' => 'required|string|unique:enterprises,cpf_cnpj_enterprise,'. ($enterpriseId ? $enterpriseId->id : null),
            'address_enterprise' => 'required|string',
            'cep_enterprise' => 'required|string',
            'city_enterprise' => 'required|string',
            'state_enterprise' => 'required|string',
            'validade' => 'required_if:password,!=,null|date_format:Y-m-d,',

            'name' => 'required_if:name,!=null|string',
            'email' => 'required_if:email,!=null|email|unique:users,email',
            'password' => 'required_if:password,!=,null',
        ];
    }

    public function messages(): array
    {
        return [
            'name_enterprise.required' => 'Campo nome da empresa é obrigatorio',
            'cpf_cnpj_enterprise.unique' => 'CPF/CNPJ já existe',
            'cpf_cnpj_enterprise.required' => 'Campo CPF/CNPJ é obrigatorio',
            'city_enterprise.required' => 'Campo CPF/CNPJ é obrigatorio',
            'address_enterprise.required' => 'Campo Endereço é obrigatorio',
            'cep_enterprise.required' => 'Campo CEP é obrigatorio',
            'state_enterprise.required' => 'Campo Estado é obrigatorio',
            'validade.required_if' => 'Campo Validade é obrigatorio',
            'validade.date_format' => "Formato invalido",

            "name.required" => "Campo Nome é obrigatorio",
            "email.required" => "Campo E-mail é obrigatorio",
            "email.email" => "Email informado não é valido",
            "email.unique" => "Email já em uso",
            "password.required" => "Campo Senha é obrigatorio",

        ];
    }
}
