<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->enum('type_moviment', ['ENTRADA', 'SAIDA'])->default('ENTRADA');
            $table->integer('qtd_stock');
            $table->integer('note_number')->nullable();
            $table->float('price_cost')->nullable()->default(0);
            $table->float('total_value')->nullable()->default(0);
            $table->string('motive');
            $table->foreignId('operation_id')->nullable()->constrained('operations');
            $table->foreignId('provider_id')->nullable()->constrained('clients');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoks');
    }
};
