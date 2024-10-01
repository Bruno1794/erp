<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function show(): JsonResponse
    {
        $userLogado = Auth::user();
        $categorys = Category::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_category', 'ATIVO')
            ->get();
        return response()->json([
            'success' => true,
            'categorys' => $categorys,
            'message' => 'Lista de Categorias',
        ]);
    }

    public function showInctive(): JsonResponse
    {
        $userLogado = Auth::user();
        $categorys = Category::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_category', 'INATIVO')
            ->get();
        return response()->json([
            'success' => true,
            'categorys' => $categorys,
            'message' => 'Lista de Categorias Inativas',
        ]);
    }

    public function showCategory(Category $category): JsonResponse
    {
        $userLogado = Auth::user();
        if ($userLogado->enterprise_id === $category->enterprise_id) {
            $category = Category::where('id', $category->id)->first();
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Listada de categorias',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Categoria não encontrada',
            ], 400);
        }
    }//

    public function store(CategoryRequest $request): JsonResponse
    {
        DB::beginTransaction();
        $userLogado = Auth::user();
        try {
            $category = Category::create([
                'name_category' => $request->name_category,
                'enterprise_id' => $userLogado->enterprise_id,


            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Categoria criada com sucesso'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Falha ao cadastrar category' . $exception->getMessage()
            ], 400);
        }
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $category->enterprise_id) {
                $category->update([
                    'name_category' => $request->name_category
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'category' => $category,

                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria nao encontrada'
                ], 400);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
            ], 400);
        }
    }

    public function updateInactive(Request $request, Category $category): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $category->enterprise_id) {
                $category->update([
                    'status_category' => $request->status_category
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'category' => $category,

                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoria nao encontrada'
                ], 400);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Falha ao cadastrar category' . $exception->getMessage()
            ], 400);
        }
    }
}
