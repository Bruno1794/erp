<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nome_client');
            $table->string('email_client')->nullable();
            $table->string('fone_client');
            $table->enum('type_partner', ['CLIENTE', 'FORNECEDOR'])->default('CLIENTE');
            $table->enum('type_client', ['PF', 'PJ'])->default('PF');
            $table->string('cpf_cnpj_client');
            $table->string('date_birth_client')->nullable();
            $table->string('rg_ie_client')->nullable();
            $table->string('address_client')->nullable();
            $table->string('number_client')->nullable();
            $table->string('city_client')->nullable();
            $table->string('state_client')->nullable();
            $table->enum('status_client', ['ATIVO', 'INATIVO'])->default('ATIVO');
            $table->string('observation_client')->nullable();
            $table->foreignId('enterprise_id')->constrained('enterprises');
            $table->date('date_register')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
