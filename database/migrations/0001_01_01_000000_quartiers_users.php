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
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('quartier_id')
                ->references('id')->on('quartiers')
                ->onDelete('set null');
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
