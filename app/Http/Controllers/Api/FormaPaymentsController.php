<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormaPaymentsRequest;
use App\Models\Form_Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormaPaymentsController extends Controller
{
    //
    public function show(): JsonResponse
    {
        $userLogado = Auth::user();
        $payments = Form_Payment::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_payments', 'ATIVO')
            ->where('internal_payment', 0)
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments
        ], 200);
    }
    public function showInternal(): JsonResponse
    {
        $userLogado = Auth::user();
        $payments = Form_Payment::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_payments', 'ATIVO')
            ->where('internal_payment', 1)
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments
        ], 200);
    }


    public function showInativo(): JsonResponse
    {
        $userLogado = Auth::user();
        $payments = Form_Payment::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_payments', 'INATIVO')
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments
        ], 200);
    }

    public function store(FormaPaymentsRequest $request): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            $payment = Form_Payment::create([
                'name_payments' => $request->name_payments,
                'type_payments' => $request->type_payments,
                'internal_payment' => $request->internal_payment,
                'enterprise_id' => $userLogado->enterprise_id,
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'payment' => $payment,
                'mesagem' => 'Salvo com sucesso'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                    'success' => false,
                    'mesagem' => $exception->getMessage()
                ]
            );
        }
    }

    public function update(Request $request, Form_Payment $payment): JsonResponse
    {

        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $payment->enterprise_id) {
                $payment->update([
                    'name_payments' => $request->name_payments,
                    'type_payments' => $request->type_payments,
                    'internal_payment' => $request->internal_payment
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'payment' => $payment,
                    'mesagem' => 'Alterdo com sucesso'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mesagem' => 'Forma de pagamento nao pertece a empresa logada'
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mesagem' => $exception->getMessage()
            ]);
        }
    }

    public function updateStatus(Request $request, Form_Payment $payment): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $payment->enterprise_id) {
                $payment->update([

                    'status_payments' => $request->status_payments
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'payment' => $payment,
                    'mesagem' => 'Alterdo com sucesso'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mesagem' => 'Forma de pagamento nao pertece a empresa logada'
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'mesagem' => $exception->getMessage()
            ]);
        }
    }
}
