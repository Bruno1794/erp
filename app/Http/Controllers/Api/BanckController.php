<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banck;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BanckController extends Controller
{
    //
    public function show(): JsonResponse
    {
        $userLogado = Auth::user();
        $banks = Banck::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_bank', 'ATIVO')
            ->get();

        return response()->json([
            'success' => true,
            'banks' => $banks,

        ], 200);
    }

    public function showInativo(): JsonResponse
    {
        $userLogado = Auth::user();
        $banks = Banck::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_bank', 'INATIVO')
            ->get();

        return response()->json([
            'success' => true,
            'banks' => $banks,
            'message' => 'Bancos encontrados',
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $userLogado = Auth::user();
            $bank = Banck::create([
                'name_bank' => $request->name_bank,
                'enterprise_id' => $userLogado->enterprise_id
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'bank' => $bank,
                'message' => 'Banco cadastrado com sucesso!'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,

                'message' => $exception->getMessage()
            ]);
        }
    }

    public function update(Request $request, Banck $banck): JsonResponse
    {

        $userLogado = Auth::user();

        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $banck->enterprise_id) {
                $banck->update([
                    'name_bank' => $request->name_bank
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'bank' => $banck,
                    'message' => 'Banco atualizado com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Banco nao pertece ao usuario logado"
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function updateStatus(Request $request, Banck $banck): JsonResponse
    {

        $userLogado = Auth::user();

        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $banck->enterprise_id) {
                $banck->update([
                    'status_bank' => $request->status_bank
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'bank' => $banck,
                    'message' => 'Banco atualizado com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Banco nao pertece ao usuario logado"
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
