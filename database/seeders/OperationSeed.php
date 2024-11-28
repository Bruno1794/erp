<?php

namespace Database\Seeders;

use App\Models\Operation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperationSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        if (!Operation::where('operation_name', 'Compra de mercadorias')->first()) {
            Operation::create([
                'operation_name' => 'Compra de mercadorias',
                'description' => '',
                'type_operation' => 'ENTRADA',
                'create_movement' => 'CP',
            ]);
        }

        if (!Operation::where('operation_name', 'Devoluções de clientes')->first()) {
            Operation::create([
                'operation_name' => 'Devoluções de clientes',
                'description' => '',
                'type_operation' => 'ENTRADA',
                'create_movement' => 'NENHUM',
            ]);
        }

        if (!Operation::where('operation_name', 'Ajuste Estoque')->first()) {
            Operation::create([
                'operation_name' => 'Ajuste Estoque',
                'description' => 'Correção de saldo de estoque',
                'type_operation' => 'ENTRADA',
                'create_movement' => 'NENHUM',
            ]);
        }

        if (!Operation::where('operation_name', 'Bonificações ou brindes recebidos')->first()) {
            Operation::create([
                'operation_name' => 'Bonificações ou brindes recebidos',
                'description' => 'Entrada sem custo financeiro.',
                'type_operation' => 'ENTRADA',
                'create_movement' => 'NENHUM',
            ]);
        }

        /*SAIDAS*/

        if (!Operation::where('operation_name', 'Venda de mercadorias')->first()) {
            Operation::create([
                'operation_name' => 'Venda de mercadorias',
                'description' => '',
                'type_operation' => 'SAIDA',
                'create_movement' => 'CR',
            ]);
        }

        if (!Operation::where('operation_name', 'Ajuste Estoque Saida')->first()) {
            Operation::create([
                'operation_name' => 'Ajuste Estoque Saida',
                'description' => '',
                'type_operation' => 'SAIDA',
                'create_movement' => 'NENHUM',
            ]);
        }

        if (!Operation::where('operation_name', 'Consumo interno')->first()) {
            Operation::create([
                'operation_name' => 'Consumo interno',
                'description' => '',
                'type_operation' => 'SAIDA',
                'create_movement' => 'NENHUM',
            ]);
        }

        if (!Operation::where('operation_name', 'Brindes')->first()) {
            Operation::create([
                'operation_name' => 'Brindes',
                'description' => 'Apenas registro da saída.',
                'type_operation' => 'SAIDA',
                'create_movement' => 'NENHUM',
            ]);
        }

        if (!Operation::where('operation_name', 'Troca de produto em garantia')->first()) {
            Operation::create([
                'operation_name' => 'Troca de produto em garantia',
                'description' => 'Apenas para ajustar o estoque',
                'type_operation' => 'SAIDA',
                'create_movement' => 'NENHUM',
            ]);
        }
    }
}
