<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //
    public function show(): JsonResponse
    {
        $userLogado = Auth::user();
        $porducts = Product::with('category')
            ->with('ncm')
            ->with('unit')
            ->where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_product', "ATIVO")
            ->get();
        return response()->json([
            'success' => true,
            'products' => $porducts
        ]);
    }

    public function showProduct(Product $product): JsonResponse
    {
        $userLogado = Auth::user();
        if ($userLogado->enterprise_id === $product->enterprise_id) {
            $porducts = Product::with('category')
                ->with('ncm')
                ->with('unit')
                ->where('id', $product->id)
                ->first();
            return response()->json([
                'success' => true,
                'products' => $porducts
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Produto nao encontrado"
            ]);
        }
    }

    public function showInativo(): JsonResponse
    {
        $userLogado = Auth::user();
        $porducts = Product::with('category')
            ->with('ncm')
            ->with('unit')
            ->where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_product', "INATIVO")
            ->get();
        return response()->json([
            'success' => true,
            'products' => $porducts
        ]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            $product = Product::create([
                'name_product' => $request->name_product,
                'manage_stock' => $request->manage_stock,
                'barcode' => $request->barcode,
                'ncm_id' => $request->ncm_id,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'stock_min' => $request->stock_min,
                'price_sale' => $request->price_sale,
                'price_cost' => $request->price_cost,
                'enterprise_id' => $userLogado->enterprise_id,
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'product' => $product,
                'message' => 'Produto cadastrado com sucesso!'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao cadastrar produto! {$exception->getMessage()}"
            ]);
        }
    }

    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $product->enterprise_id) {
                $product->update([
                    'name_product' => $request->name_product,
                    'manage_stock' => $request->manage_stock,
                    'barcode' => $request->barcode,
                    'ncm_id' => $request->ncm_id,
                    'category_id' => $request->category_id,
                    'unit_id' => $request->unit_id,
                    'stock_min' => $request->stock_min,
                    'price_sale' => $request->price_sale,
                    'price_cost' => $request->price_cost,
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'product' => $product,
                    'message' => 'Produto Alterado com sucesso'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Produto nao encontrado'
                ], 400);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao cadastrar produto!}"
            ], 400);
        }
    }

    public function updateStatus(ProductRequest $request, Product $product): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $product->enterprise_id) {
                $product->update([
                    'status_product' => $request->status_product,

                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'product' => $product,
                    'message' => 'Produto Alterado com sucesso'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Produto nao encontrado'
                ], 400);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Falha ao cadastrar produto!"
            ], 400);
        }
    }
}


