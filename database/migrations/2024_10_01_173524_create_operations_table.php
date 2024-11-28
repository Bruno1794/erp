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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->string('operation_name');
            $table->string('description')->nullable();
            $table->enum('type_operation',['ENTRADA','SAIDA'])->default('ENTRADA');
            $table->enum('create_movement',['NENHUM ','CP','CR'])->default('NENHUM ');
            $table->foreignId('enterprise_id')->nullable()->constrained('enterprises');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
