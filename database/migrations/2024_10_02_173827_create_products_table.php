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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_product');
            $table->boolean('manage_stock')->default(0);//0-nao e 1-para sim
            $table->string('barcode')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId('ncm_id')->nullable()->constrained('ncms');
            $table->foreignId('unit_id')->nullable()->constrained('unit_sizes');
            $table->integer('qtd_stock')->nullable()->default(0);
            $table->integer('stock_min')->nullable()->default(0);
            $table->float('price_sale')->nullable()->default(0);
            $table->enum('status_product', ['ATIVO', 'INATIVO'])->default('ATIVO');
            $table->foreignId('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
