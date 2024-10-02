<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NcmRequest;
use App\Models\Ncm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NcmController extends Controller
{
    //
    public function show(): JsonResponse
    {
        $userLogado = Auth::user();
        $ncms = Ncm::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_ncm', 'ATIVO')
            ->get();
        return response()->json([
            'status' => true,
            'ncms' => $ncms
        ]);
    }

    public function showInativo(): JsonResponse
    {
        $userLogado = Auth::user();
        $ncms = Ncm::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_ncm', 'INATIVO')
            ->get();
        return response()->json([
            'status' => true,
            'ncms' => $ncms
        ]);
    }

    public function store(NcmRequest $request): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            $ncm = Ncm::create([
                'name_ncm' => $request->name_ncm,
                'cod_ncm' => $request->cod_ncm,
                'status_ncm' => $request->status_ncm,
                'enterprise_id' => $userLogado->enterprise_id,
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'ncm' => $ncm

            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao cadastrar ncm:"
            ], 400);
        }
    }

    public function update(NcmRequest $request, Ncm $ncm): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $ncm->enterprise_id) {
                $ncm->update([
                    'name_ncm' => $request->name_ncm,
                    'cod_ncm' => $request->cod_ncm
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'ncm' => $ncm,
                    'message' => "NCM alterado com sucesso!"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "NCM nao encontrado"
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao atualizar ncm:"
            ]);
        }
    }

    public function updateStatus(NcmRequest $request, Ncm $ncm): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $ncm->enterprise_id) {
                $ncm->update([

                    'status_ncm' => $request->status_ncm
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'ncm' => $ncm,
                    'message' => "NCM alterado com sucesso!"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "NCM nao encontrado"
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao atualizar ncm:"
            ]);
        }
    }


}
