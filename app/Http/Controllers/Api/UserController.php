<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
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
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao cadastrar usuario!",
            ]);
        }
    }

    public function update(Request $request, User $user): JsonResponse
    {
        DB::beginTransaction();
        $userLogado = Auth::user();
        if($userLogado->enterprise_id === $user->enterprise_id){
            $user->update([
               "name" => $request->name,
               "email" => $request->email,
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'Usuario atualizado com sucesso!'
            ]);
        }else{
            return response()->json([
                'success' => false,
            ]);
        }


    }


}

