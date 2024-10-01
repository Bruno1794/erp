<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit_Size;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    //
    public function show()
    {
        $userLogado = Auth::user();
        $units = Unit_Size::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_unit', 'ATIVO')
            ->get();
        return response()->json([
            'success' => true,
            'units' => $units
        ]);
    }

    public function showUnit(Unit_Size $unit)
    {
        $userLogado = Auth::user();
        if ($userLogado->enterprise_id === $unit->enterprise_id) {
            $units = Unit_Size::where('enterprise_id', $userLogado->enterprise_id)
                ->where('id', $unit->id)
                ->first();
            return response()->json([
                'success' => true,
                'units' => $units
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Unidade nao encontrada"
            ]);
        }
    }

    public function showInative()
    {
        $userLogado = Auth::user();
        $units = Unit_Size::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_unit', 'INATIVO')
            ->get();
        return response()->json([
            'success' => true,
            'units' => $units
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            $unit = Unit_Size::create([
                'name_unit' => $request->name_unit,
                'status_unit' => $request->status_unit,
                'enterprise_id' => $userLogado->enterprise_id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'unit' => $unit,
                'message' => 'Unidade cadastrada com sucesso!'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao cadastrar unidade de medida"
            ], 400);
        }
    }

    public function update(Request $request, Unit_Size $unit): JsonResponse
    {
        $userLogado = Auth::user();
        try {
            if ($userLogado->enterprise_id === $unit->enterprise_id) {
                $unit->update([
                    'name_unit' => $request->name_unit
                ]);
                return response()->json([
                    'success' => true,
                    'unit' => $unit,
                    'message' => 'Unidade atualizada com sucesso!',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unidade de medida nao encontrada'
                ], 400);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possivel atualizar a unidade de medida'
            ], 400);
        }
    }

    public function updateStatus(Request $request, Unit_Size $unit): JsonResponse
    {
        $userLogado = Auth::user();
        try {
            if ($userLogado->enterprise_id === $unit->enterprise_id) {
                $unit->update([
                    'status_unit' => $request->status_unit
                ]);
                return response()->json([
                    'success' => true,
                    'unit' => $unit,
                    'message' => 'Unidade atualizada com sucesso!',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unidade de medida nao encontrada'
                ], 400);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possivel atualizar a unidade de medida'
            ], 400);
        }
    }
}
