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
        //
        Schema::table('users', function (Blueprint $table) {
            $table->enum('level', ['SUPER_ADMIN', 'ADMINISTRADOR', 'FUNCIONARIO'])->default('ADMINISTRADOR')
                ->after('email');
            $table->foreignId('enterprise_id')->constrained('enterprises');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_enterprise_id_foreign');
        });
    }
};
