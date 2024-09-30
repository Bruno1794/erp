<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use App\Models\User;
use Carbon\Carbon;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function login(Request $request): JsonResponse
    {
        /*$time = strtotime($data);
          $minhaData = Carbon::createFromDate($data);*/
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $enterprise = Enterprise::where('id', $user->enterprise_id)->first();
            if (date('Y-m-d') <= $enterprise->validade) {
                $token = $request->user()->createToken('token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => $user,
                    'message' => 'Logado com sucesso!',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Conta expirada"
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Usuario ou senha incorretos"
            ], 401);
        }
    }

    public function logout(): JsonResponse
    {
        $userLogado = Auth::check();
        if ($userLogado) {
            $user = User::where('id', Auth::id())->first();
            $user->tokens()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso!'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Falha ao fazer logout!'
            ]);
        }
    }
}
