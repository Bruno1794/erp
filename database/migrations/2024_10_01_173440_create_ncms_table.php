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
        Schema::create('ncms', function (Blueprint $table) {
            $table->id();
            $table->string('name_ncm');
            $table->string('cod_ncm');
            $table->enum('status_ncm', ['ATIVO', 'INATIVO'])->default('ATIVO');
            $table->foreignId('enterprise_id')->constrained('enterprises');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ncms');
    }
};
