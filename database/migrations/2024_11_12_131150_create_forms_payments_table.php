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
        Schema::create('forms_payments', function (Blueprint $table) {
            $table->id();
            $table->string('name_payments');
            $table->enum('status_payments', ['ATIVO', 'INATIVO'])->default('ATIVO');
            $table->enum('type_payments', ['PIX', 'DEBITO', 'CREDITO', 'DINHEIRO', 'CHEQUE', 'CREDIARIO'])
                ->default('DINHEIRO');
            $table->foreignId('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms_payments');
    }
};
