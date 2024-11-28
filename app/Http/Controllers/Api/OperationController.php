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
        $operations = Operation::get();
        return response()->json([
            'success' => true,
            'operations' => $operations
        ]);
    }
}
