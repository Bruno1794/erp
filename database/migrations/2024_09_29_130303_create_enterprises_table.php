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
        Schema::create('enterprises', function (Blueprint $table) {
            $table->id();
            $table->string('name_enterprise');
            $table->string('cpf_cnpj_enterprise');
            $table->string('rg_ie_enterprise')->nullable();
            $table->string('address_enterprise');
            $table->string('number_enterprise')->nullable();
            $table->string('cep_enterprise');
            $table->string('city_enterprise');
            $table->string('state_enterprise');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprises');
    }
};
