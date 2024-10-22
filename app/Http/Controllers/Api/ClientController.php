<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    //
    public function show(): JsonResponse
    {
        $userLogado = Auth::user();
        $clients = Client::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_client', 'ativo')
            ->where('type_partner', 'CLIENTE')
            ->get();
        return response()->json([
            'success' => 'true',
            'clients' => $clients
        ], 200);
    }

    public function showInactive(): JsonResponse
    {
        $userLogado = Auth::user();
        $inactives = Client::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_client', 'INATIVO')
            ->get();
        return response()->json([
            'success' => 'true',
            'inactives' => $inactives
        ], 200);
    }

    public function showProviders(): JsonResponse
    {
        $userLogado = Auth::user();
        $providers = Client::where('enterprise_id', $userLogado->enterprise_id)
            ->where('status_client', 'ativo')
            ->where('type_partner', 'FORNECEDOR')
            ->get();
        return response()->json([
            'success' => 'true',
            'providers' => $providers
        ], 200);
    }

    public function showClient(Client $client): JsonResponse
    {
        $userLogado = Auth::user();
        if ($userLogado->enterprise_id === $client->enterprise_id) {
            $client = Client::where('id', $client->id)
                ->first();
            return response()->json([
                'success' => 'true',
                'client' => $client
            ], 200);
        } else {
            return response()->json([
                'success' => 'false',
                'message' => "Nenhum cliente encontrado"
            ], 400);
        }
    }

    public function store(ClientRequest $request): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            $client = Client::create([
                'nome_client' => $request->nome_client,
                'fone_client' => $request->fone_client,
                'type_partner' => $request->type_partner,
                'type_client' => $request->type_client,
                'cpf_cnpj_client' => $request->cpf_cnpj_client,
                'date_birth_client' => $request->date_birth_client,
                'rg_ie_client' => $request->rg_ie_client,
                'address_client' => $request->address_client,
                'number_client' => $request->number_client,
                'city_client' => $request->city_client,

                'email_client' => $request->email_client,
                'observation_client' => $request->observation_client,
                'enterprise_id' => $userLogado->enterprise_id,
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'client' => $client,
                'message' => 'Client cadastrado com sucesso!'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Falha ao cadastrar o cliente!'
            ], 400);
        }
    }

    public function update(ClientRequest $request, Client $client): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $client->enterprise_id) {
                $client->update([
                    'nome_client' => $request->nome_client,
                    'fone_client' => $request->fone_client,
                    'type_partner' => $request->type_partner,
                    'cpf_cnpj_client' => $request->cpf_cnpj_client,
                    'date_birth_client' => $request->date_birth_client,
                    'rg_ie_client' => $request->rg_ie_client,
                    'address_client' => $request->address_client,
                    'number_client' => $request->number_client,
                    'city_client' => $request->city_client,
                    'observation_client' => $request->observation_client,
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'client' => $client,
                    'message' => 'Client atualizado com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente nao pertece a essa empresa'
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Falha ao atualizar o cliente! ' . $exception->getMessage()
            ]);
        }
    }

    public function updateStatus(Request $request, Client $client): JsonResponse
    {
        $userLogado = Auth::user();
        DB::beginTransaction();
        try {
            if ($userLogado->enterprise_id === $client->enterprise_id) {
                $client->update([

                    'status_client' => $request->status_client,

                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'client' => $client,
                    'message' => 'Client atualizado com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente nao pertece a essa empresa'
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Falha ao atualizar o cliente! ' . $exception->getMessage()
            ]);
        }
    }
}
