<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class NcmRequest extends FormRequest
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
        $userid = $this->route('ncm');
        //dd( $userid->cod_ncm);
        $userLogado = Auth::user();
        return [
            'name_ncm' => 'required_if:name_ncm,!=,null|string',
            'cod_ncm' => 'required_if:cod_ncm,!=,null|max:8|unique:ncms,cod_ncm,'. ($userid ? $userid->id : ',id,enterprise_id, ' . $userLogado->enterprise_id),
            'status_ncm' => 'required_if:status_ncm,!=,null',
         /*   required_if:password,!=,null*/


        ];
    }

    public function messages(): array
    {
        return [
            'name_ncm.required' => 'Campo nome é obrigatorio',
            'cod_ncm.required_if' => 'Codigo do NCM é obrigatorio',
            'cod_ncm.unique' => 'NCM já cadastrado',
            'cod_ncm.max' => 'Codigo de ate 8 carcteres',
            'status_ncm.required' => 'Status Obrigatorio',
        ];
    }
}
