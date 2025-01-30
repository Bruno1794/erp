<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StokRequest;
use App\Models\Client;
use App\Models\Debt;
use App\Models\Operation;
use App\Models\Product;
use App\Models\Stok;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function show(): JsonResponse
    {
        $userLogado = Auth::user();
        $stoks = Stok::where('enterprise_id', $userLogado->enterprise_id)
            ->with('product')
            ->with('user')
            ->get();
        return response()->json([
            'success' => true,
            'stoks' => $stoks
        ], 200);
    }

    public function showManage(Request $request): JsonResponse
    {
        $userLogado = Auth::user();
        $product = Product::where('name_product', 'like', '%' . $request->name . '%')
            ->where('enterprise_id', $userLogado->enterprise_id)
            ->where('manage_stock', '1')
            ->get();
        if ($product->count() > 0) {
            return response()->json([
                'success' => true,
                'products' => $product
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum produto encontrado'
            ], 400);
        }
    }

    public function showProvider(Request $request): JsonResponse
    {
        $userLogado = Auth::user();
        $provider = Client::where('nome_client', 'like', '%' . $request->name . '%')
            ->where('enterprise_id', $userLogado->enterprise_id)
            ->where('type_partner', 'FORNECEDOR')
            ->get();
        if ($provider->count() > 0) {
            return response()->json([
                'success' => true,
                'providers' => $provider
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum produto encontrado'
            ], 400);
        }
    }

    public function store(Request $request, StokRequest $stokvalidate): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            $product_stock = Product::where('enterprise_id', $userLogado->enterprise_id)
                ->where('manage_stock', '1')
                ->where('id', $request->product_id)->first(['id', 'qtd_stock']);

            if ($product_stock && $request->type_moviment === 'SAIDA' && $product_stock->qtd_stock > 0 ||
                $request->type_moviment === 'ENTRADA' && $product_stock->qtd_stock >= 0) {
                $total = $request->qtd_stock * $request->price_cost;

                $stok = Stok::create([
                    'product_id' => $request->product_id,
                    'type_moviment' => $request->type_moviment,
                    'qtd_stock' => $request->qtd_stock,
                    'note_number' => $request->note_number,
                    'price_cost' => $request->price_cost,
                    'total_value' => $total,
                    'operation_id' => $request->operation_id,
                    'motive' => $request->motive,
                    'provider_id' => $request->provider_id,
                    'enterprise_id' => $userLogado->enterprise_id,
                    'user_id' => $userLogado->id,
                ]);

                $operation = Operation::where('id', $request->operation_id)->first();

                if ($stok && $operation->create_movement === 'CP' && $stok->total_value > 0 &&
                    !str_contains($request->parcel, '/') && $request->parcel > 0) {
                    $parcelas = [];

                    if ($request->parcel > 1) {
                        $valorParcela = floor($stok->total_value / $request->parcel * 100) / 100;
                        for ($i = 1; $i <= $request->parcel; $i++) {
                            $parcelas[$i] = $valorParcela;
                        }
                        $diferenca = $stok->total_value - array_sum($parcelas);
                        $parcelas[$request->parcel - 1] += $diferenca;
                    }

                    for ($i = 1; $i <= $request->parcel; $i++) {
                        Debt::create([
                            'name_debit' => $stok->type_moviment . " DE ESTOQUE | REF:" . $stok->id,
                            'value_total_debit' => $parcelas ? $parcelas[$i] : $stok->total_value,
                            'parcel' => $i . "/" . $request->parcel,
                            'date_venciment' => $parcelas ? Carbon::now()->copy()->addMonths($i) :
                                Carbon::now()->copy()->addMonths(1), //Carbon::createFromFormat('Y-m-d', $request->date_venciment)
                            'number_note' => $stok->note_number,
                            'number_check' => $request->number_check,
                            'banck_transmitter_cheque' => $request->banck_transmitter_cheque,
                            'forms_payments_id' => $stokvalidate->forms_payments_id,
                            'banck_id' => $stokvalidate->banck_id,
                            'stok_id' => $stok->id,
                            'enterprise_id' => $userLogado->enterprise_id,
                            'user_id' => $userLogado->id,
                            'provider_id' => $stok->provider_id,
                        ]);
                    }
                } else if($stok && str_contains($request->parcel, '/') && $operation->create_movement === 'CP'){
                    ##PARCELAMENTO EM DIAS EXEMPLO 10/20/30 DIAS

                    /*CRIOU UM ARRAY DE DIAS DE PARCELAMENTOS E REMOVO STRING Q VINHER VAZIA*/
                    $arrayParecelas = collect(explode('/', $request->parcel))
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
                        $valorParcela = floor($stok->total_value / $qtdParecelas * 100) / 100;
                        for ($i = 1; $i <= $qtdParecelas; $i++) {
                            $parcelas[$i] = $valorParcela;
                        }
                        $diferenca = $stok->total_value - array_sum($parcelas);
                        $parcelas[$qtdParecelas - 1] += $diferenca;
                    }
                    ##FIM


                    ## SALVO AS INFORMAÇOES DENTRO DO ARAY DADOS, COM OS DIAS DE PARCELAMENTO CONFOME PASSANDO
                    ## DENTRO DO ARRAY PARECELAS
                    $dados = [];
                    foreach ($arrayParecelas as $key => $dias) {
                        $dados[] = [
                            'name_debit' => $stok->type_moviment . " DE ESTOQUE | REF:" . $stok->id,
                            'value_total_debit' => $parcelas ? $parcelas[$key + 1] : $stok->total_value,
                            'operation_id' => $request->operation_id,
                            'parcel' => $key + 1 . "/" . $qtdParecelas,
                            'date_venciment' => Carbon::now()->copy()->addDays((int)$dias),
                            'number_note' => $stok->note_number,
                            'number_check' => $request->number_check,
                            'banck_transmitter_cheque' => $request->banck_transmitter_cheque,
                            'forms_payments_id' => $stokvalidate->forms_payments_id,
                            'banck_id' => $stokvalidate->banck_id,
                            'stok_id' => $stok->id,
                            'enterprise_id' => $userLogado->enterprise_id,
                            'user_id' => $userLogado->id,
                            'provider_id' => $stok->provider_id,
                        ];
                    }
                    ##FIM

                    ## PEGAO O ARRAY DADOS E SALVO NO BANCO DE DADOS
                    foreach ($dados as $dado) {
                        Debt::create($dado);
                    }
                    ##FIM
                }else{
                    if($operation->create_movement === 'CP') {
                        Debt::create([
                            'name_debit' => $stok->type_moviment . " DE ESTOQUE | REF:" . $stok->id,
                            'number_note' => $stok->note_number,
                            'number_check' => $request->number_check,
                            'banck_transmitter_cheque' => $request->banck_transmitter_cheque,
                            'value_total_debit' => $stok->total_value,
                            'parcel' => 'AVISTA',
                            'date_venciment' => Carbon::now(),
                            'date_payment' => $request->date_payment,
                            'value_paid' => $request->value_paid,
                            'description' => $request->description,
                            'enterprise_id' => $userLogado->enterprise_id,
                            'stok_id' => $stok->id,
                            'user_id' => $userLogado->id,
                            'forms_payments_id' => $stokvalidate->forms_payments_id,
                            'banck_id' => $stokvalidate->banck_id,
                        ]);
                    }
                }

                switch ($stok->type_moviment) {
                    case 'ENTRADA':
                        $sumStock = $stok->qtd_stock + $product_stock->qtd_stock;
                        $product_stock->update([
                            'qtd_stock' => $sumStock,
                        ]);
                        break;
                    default :
                        $sumStock = $product_stock->qtd_stock - $stok->qtd_stock;
                        $product_stock->update([
                            'qtd_stock' => $sumStock,
                        ]);
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'stock' => $stok,
                    'message' => "Estoque cadastrado com sucesso!"
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Produto nao gerencia estoque ou Estouque zerado"
                ], 400);
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
