<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DebitRequest;
use App\Models\Debt;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DebtController extends Controller
{
    //
    public function show(Request $request): JsonResponse
    {
        $userLogado = Auth::user();
        $hoje = Carbon::today();
        $dataLimite = $hoje->copy()->addDays(30);

        $debts = $request->dataInicio && $request->dataFim ?
            Debt::with('user')->with('forms_payments')->with('provider')->with('banck')
                ->where('enterprise_id', $userLogado->enterprise_id)
                ->whereBetween('date_venciment', [Carbon::parse($request->dataInicio), Carbon::parse($request->dataFim)]
                )
                ->where('type_debit', 'PENDENTE')
                ->where('status_debit', 'ATIVO')
                ->orderBy('date_venciment')
                ->get() :

            Debt::with('user')->with('forms_payments')->with('provider')->with('banck')
                ->where('enterprise_id', $userLogado->enterprise_id)
                ->whereBetween('date_venciment', [$hoje, $dataLimite])
                ->whereYear('date_venciment', Carbon::now()->year)
                ->where('type_debit', 'PENDENTE')
                ->where('status_debit', 'ATIVO')
                ->orderBy('date_venciment')
                ->get();

        $valorDebit = $debts->sum('value_total_debit');

        return response()->json([
            'success' => true,
            'debts' => $debts,
            'value_debit' => $valorDebit,
        ], 200);
    }

    public function store(DebitRequest $debitRequest): JsonResponse
    {

        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if (!str_contains($debitRequest->parcel, '/') && $debitRequest->parcel > 1) {

                $parcelas = [];

                    // Calcular o valor base da parcela (arredondado para duas casas decimais)
                    $valorParcela = floor($debitRequest->value_total_debit / $debitRequest->parcel * 100) / 100;
                    // Inicializar um array para armazenar as parcelas
                    // Calcular as parcelas usando um loop for
                    for ($i = 1; $i <= $debitRequest->parcel; $i++) {
                        // Atribuir o valor base da parcela
                        $parcelas[$i] = $valorParcela;
                    }
                    // Ajustar a última parcela para cobrir a diferença, se houver
                    $diferenca = $debitRequest->value_total_debit - array_sum($parcelas);
                    $parcelas[$debitRequest->parcel - 1] += $diferenca;


                for ($i = 1; $i <= $debitRequest->parcel; $i++) {
                    Debt::create([
                        'name_debit' => $debitRequest->name_debit,
                        'number_note' => $debitRequest->number_note,
                        'number_check' => $debitRequest->number_check,
                        'banck_transmitter_cheque' => $debitRequest->banck_transmitter_cheque,
                        'value_total_debit' => $debitRequest ? $parcelas[$i] : $debitRequest->value_total_debit,
                        'parcel' => $i . "/" . $debitRequest->parcel,
                        'date_venciment' => Carbon::now()->copy()->addMonths($i),
                        'date_payment' => $debitRequest->date_payment,
                        'value_paid' => $debitRequest->value_paid,
                        'description' => $debitRequest->description,
                        'enterprise_id' => $userLogado->enterprise_id,
                        'user_id' => $userLogado->id,
                        'forms_payments_id' => $debitRequest->forms_payments_id,
                        'banck_id' => $debitRequest->banck_id,
                    ]);
                }
            } else if($debitRequest->parcel === null || $debitRequest->parcel === '0'){
                ## Lancar conta a visa

                Debt::create([
                    'name_debit' => $debitRequest->name_debit,
                    'number_note' => $debitRequest->number_note,
                    'number_check' => $debitRequest->number_check,
                    'banck_transmitter_cheque' => $debitRequest->banck_transmitter_cheque,
                    'value_total_debit' => $debitRequest->value_total_debit ,
                    'parcel' => 'AVISTA',
                    'date_venciment' => Carbon::now(),
                    'date_payment' => $debitRequest->date_payment,
                    'value_paid' => $debitRequest->value_paid,
                    'description' => $debitRequest->description,
                    'enterprise_id' => $userLogado->enterprise_id,
                    'user_id' => $userLogado->id,
                    'forms_payments_id' => $debitRequest->forms_payments_id,
                    'banck_id' => $debitRequest->banck_id,
                ]);

            }
            else {
                ##PARCELAMENTO EM DIAS EXEMPLO 10/20/30 DIAS

                /*CRIOU UM ARRAY DE DIAS DE PARCELAMENTOS E REMOVO STRING Q VINHER VAZIA*/
                $arrayParecelas = collect(explode('/', $debitRequest->parcel))
                    ->filter(function ($value) {
                        return $value !== ''; // Remove apenas strings vazias
                    })
                    ->values()
                    ->toArray();
                /*fim*/

                /*CONTO QUANTOS INTENS TEM DENTRO DO MEU ARRAY EX: 10/30/20 SERA GERARO TOTAL DE 3*/
                $qtdParecelas = collect($arrayParecelas)
                    ->filter(function ($value) {
                        return !is_null($value) && $value !== ''; // Ajuste conforme a lógica
                    })
                    ->count();
                /*fim*/

                ## CALULO AS PARCELAS E GUARDO DENTRO DO ARRAY PARCELAS[] FAZENDO O CALCULO CORRENTAMENTE DOS VALORES
                $parcelas = [];

                if ($qtdParecelas > 1) {
                    $valorParcela = floor($debitRequest->value_total_debit / $qtdParecelas * 100) / 100;
                    for ($i = 1; $i <= $qtdParecelas; $i++) {
                        $parcelas[$i] = $valorParcela;
                    }
                    $diferenca = $debitRequest->value_total_debit - array_sum($parcelas);
                    $parcelas[$qtdParecelas - 1] += $diferenca;
                }
                ##FIM


                ## SALVO AS INFORMAÇOES DENTRO DO ARAY DADOS, COM OS DIAS DE PARCELAMENTO CONFOME PASSANDO
                ## DENTRO DO ARRAY PARECELAS
                $dados = [];
                foreach ($arrayParecelas as $key => $dias) {
                    $dados[] = [
                        'name_debit' => $debitRequest->name_debit,
                        'value_total_debit' => $parcelas ? $parcelas[$key + 1] : $debitRequest->value_total_debit,
                        'operation_id' => $debitRequest->operation_id,
                        'parcel' => $key + 1 . "/" . $qtdParecelas,
                        'date_venciment' => Carbon::now()->copy()->addDays((int)$dias),
                        'number_note' => $debitRequest->number_note,
                        'number_check' => $debitRequest->number_check,
                        'banck_transmitter_cheque' => $debitRequest->banck_transmitter_cheque,
                        'forms_payments_id' => $debitRequest->forms_payments_id,
                        'banck_id' => $debitRequest->banck_id,
                        'enterprise_id' => $userLogado->enterprise_id,
                        'user_id' => $userLogado->id,
                        'provider_id' => $debitRequest->provider_id,
                    ];
                }
                ##FIM

                ## PEGAO O ARRAY DADOS E SALVO NO BANCO DE DADOS
                foreach ($dados as $dado) {
                    Debt::create($dado);
                }
                ##FIM
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Contas apagar criada com sucesso'
            ], 200);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ],400);
        }
    }

    public function update(Request $request, Debt $debt): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $debt->enterprise_id && $debt->type_debit === 'PENDENTE') {
                $debt->update([
                    'name_debit' => $request->name_debit,
                    'number_note' => $request->number_note,
                    'number_check' => $request->number_check,
                    'banck_transmitter_cheque' => $request->number_check,
                    'value_total_debit' => $request->value_total_debit,
                    'date_venciment' => $request->date_venciment,
                    'description' => $request->description,
                    'forms_payments_id' => $request->forms_payments_id,
                    'banck_id' => $request->banck_id,
                    'provider_id' => $request->provider_id,


                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'debt' => $debt,
                    'message' => 'Conta atualizada com sucesso'
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 400);
        }
        return response()->json([
            'success' => true,
        ]);
    }

    public function updateStatus(Request $request, Debt $debt): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $debt->enterprise_id && $debt->type_debit === 'PENDENTE') {
                $debt->update([
                    'status_debit' => $request->status_debit,
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'debt' => $debt,
                    'message' => 'Conta atualizada com sucesso'
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 400);
        }
        return response()->json([
            'success' => true,
        ]);
    }
}
