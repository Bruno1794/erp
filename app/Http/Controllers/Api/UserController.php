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


class UserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->level !== "SUPER_ADMIN") {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password,
                    'level' => $request->level,
                    'enterprise_id' => $userLogado->enterprise_id,
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'message' => 'Usuario cadastrado com sucesso!'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "SUPER ADMINISTRADOR nao pode cadastrar funcionarios"
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao cadastrar usuario!",
            ]);
        }
    }

    public function showProfile(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'user' => $user,
        ], 200);
    }

    public function show(): JsonResponse
    {
        $user = Auth::user();
        $users = User::where('enterprise_id', $user->enterprise_id)->get();

        return response()->json([
            'success' => true,
            'users' => $users,
        ], 200);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        DB::beginTransaction();
        $userLogado = Auth::user();
        if ($userLogado->enterprise_id === $user->enterprise_id) {
            try {
                $user->update([
                    "name" => $request->name,
                    "email" => $request->email,
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'message' => 'Usuario atualizado com sucesso!'
                ], 200);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao atualizar usuario!'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Usuario nao pertence a empresa vinculada"
            ], 400);
        }
    }

    public function updatePassoword(Request $request, User $user): JsonResponse
    {
        DB::beginTransaction();
        $userLogado = Auth::user();
        if ($userLogado->enterprise_id === $user->enterprise_id) {
            try {
                $user->update([
                    "password" => Hash::make($request->password, ['rounds' => 12]),

                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'message' => 'Senha atualizada com sucesso!'
                ], 200);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao atualizar senha!'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Usuario nao pertence a empresa vinculada"
            ], 400);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $user->enterprise_id && $user->id !== $userLogado->id) {
                $user->delete();
                DB::commit();
                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'message' => 'Usuario apagado com sucesso!'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Não e possivel remover esse usuario"
                ], 400);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao deletar usuario!",
            ]);
        }
    }


}

