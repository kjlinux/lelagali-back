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
        Schema::table('quartiers', function (Blueprint $table) {
            $table->foreignUuid('created_by')
                ->nullable()  // Ajout explicite de nullable
                ->references('id')->on('users')
                ->onDelete('set null')  // Changé en set null pour cohérence
                ->after('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('quartier_id')
                ->nullable()  // Ajout explicite de nullable
                ->references('id')->on('quartiers')
                ->onDelete('set null')
                ->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quartiers', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['quartier_id']);
        });
    }
};
