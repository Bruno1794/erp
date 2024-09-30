<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EnterpriseController extends Controller
{
    //
    public function showProfile(): JsonResponse
    {
        $usuerLogado = Auth::user();
        $profile = Enterprise::where('id', $usuerLogado->enterprise_id)->first();
        return response()->json([
            'success' => true,
            'profile' => $profile
        ], 200);
    }

    public function show(): JsonResponse
    {
        $userLogado = Auth::user();
        if ($userLogado->level === "SUPER_ADMIN") {
            $profile = Enterprise::get();
            return response()->json([
                'success' => true,
                'profile' => $profile
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Usuario nao permitido"
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $enterprise = Enterprise::create([
                'name_enterprise' => $request->name_enterprise,
                'cpf_cnpj_enterprise' => $request->cpf_cnpj_enterprise,
                'rg_ie_enterprise' => $request->rg_ie_enterprise,
                'address_enterprise' => $request->address_enterprise,
                'number_enterprise' => $request->number_enterprise,
                'cep_enterprise' => $request->cep_enterprise,
                'city_enterprise' => $request->city_enterprise,
                'state_enterprise' => $request->state_enterprise,
                'validade' => $request->validade,
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password, ['rounds' => 12]),
                'level' => $request->level,
                'enterprise_id' => $enterprise->id,
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'enterprise' => $enterprise,
                'message' => 'Empresa cadastrada com sucesso!'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                /* {$exception->getMessage()}*/
                'success' => false,
                'error' => "Falha ao cadastrar Empresa! {$exception->getMessage()}"
            ], 400);
        }
    }

    public function update(Request $request, Enterprise $enterprise): JsonResponse
    {
        DB::beginTransaction();
        try {
            $userLogado = Auth::user();

            if ($enterprise->id === $userLogado->enterprise_id) {
                $enterprise->update([
                    'name_enterprise' => $request->name_enterprise,
                    'cpf_cnpj_enterprise' => $request->cpf_cnpj_enterprise,
                    'rg_ie_enterprise' => $request->rg_ie_enterprise,
                    'number_enterprise' => $request->number_enterprise,
                    'cep_enterprise' => $request->cep_enterprise,
                    'city_enterprise' => $request->city_enterprise,
                    'state_enterprise' => $request->state_enterprise,
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'enterprise' => $enterprise,
                    'message' => 'Empresa atualizada com sucesso!'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta empresa nao pertece ao usuario logado'
                ], 400);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao atualizar Empresa!"
            ], 400);
        }
    }

    public function updateSuper(Request $request, Enterprise $enterprise): JsonResponse
    {
        DB::beginTransaction();
        try {
            $userLogado = Auth::user();

            if ($userLogado->level === "SUPER_ADMIN") {
                $enterprise->update([
                    'name_enterprise' => $request->name_enterprise,
                    'cpf_cnpj_enterprise' => $request->cpf_cnpj_enterprise,
                    'rg_ie_enterprise' => $request->rg_ie_enterprise,
                    'number_enterprise' => $request->number_enterprise,
                    'cep_enterprise' => $request->cep_enterprise,
                    'city_enterprise' => $request->city_enterprise,
                    'state_enterprise' => $request->state_enterprise,
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'enterprise' => $enterprise,
                    'message' => 'Empresa atualizada com sucesso!'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta empresa nao pertece ao usuario logado'
                ], 400);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao atualizar Empresa!"
            ], 400);
        }
    }

    public function updateValidate(Request $request, Enterprise $enterprise): JsonResponse
    {
        DB::beginTransaction();
        try {
            $userLogado = Auth::user();

            if ($userLogado->level === "SUPER_ADMIN") {
                $enterprise->update([
                    'validade' => $request->validade,
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'enterprise' => $enterprise,
                    'message' => 'Renovado com sucesso!'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possivel renovar empresa'
                ], 400);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao renovar Empresa!"
            ], 400);
        }
    }

    public function destroy(Enterprise $enterprise): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->level === "SUPER_ADMIN") {
                $enterprise->delete();
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Empresa removida com sucesso!'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao remover Empresa!'
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Existe dados vinculados a este empresa!"
            ]);
        }
    }
}
