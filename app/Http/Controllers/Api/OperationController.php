<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Operation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    //
    public function show(): JsonResponse
    {
        $operations = Operation::where('type_operation', 'ENTRADA')->get();
        return response()->json([
            'success' => true,
            'operations' => $operations
        ]);
    }

    public function showExit(): JsonResponse
    {
        $operations = Operation::where('type_operation', 'SAIDA')->get();
        return response()->json([
            'success' => true,
            'operations' => $operations
        ]);
    }
}
