<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Product;
use App\Models\Stok;
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

    public function store(Request $request): JsonResponse
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
                    'debit' => $request->debit,
                    'motive' => $request->motive,
                    'provider_id' => $request->provider_id,
                    'enterprise_id' => $userLogado->enterprise_id,
                    'user_id' => $userLogado->id,
                ]);

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
