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
        Schema::create('debt_payable', function (Blueprint $table) {
            $table->id();
            $table->string('name_debit')->nullable();
            $table->string('number_note')->nullable();
            $table->string('number_check')->nullable();
            $table->string('banck_transmitter_cheque')->nullable();
            $table->float('value_total_debit');
            $table->string('parcel')->nullable();
            $table->date('date_venciment');
            $table->date('date_payment')->nullable();
            $table->float('value_paid')->nullable();
            $table->enum('type_debit', ['PENDENTE ', 'PAGO'])->default('PENDENTE');
            $table->enum('status_debit', ['ATIVO ', 'INATIVO'])->default('ATIVO');
            $table->string('description')->nullable();
            $table->foreignId('stok_id')->nullable()->constrained('stoks');
            $table->foreignId('enterprise_id')->constrained('enterprises');
            $table->foreignId('provider_id')->nullable()->constrained('clients');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('forms_payments_id')->nullable()->constrained('forms_payments');
            $table->foreignId('banck_id')->nullable()->constrained('banks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_payable');
    }
};
