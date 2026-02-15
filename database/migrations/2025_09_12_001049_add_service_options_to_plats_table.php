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
        Schema::table('plats', function (Blueprint $table) {
            // Option pour la livraison
            $table->boolean('livraison_disponible')->default(true)->after('temps_preparation');

            // Option pour le retrait (par défaut true car tous les plats peuvent être retirés)
            $table->boolean('retrait_disponible')->default(true)->after('livraison_disponible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plats', function (Blueprint $table) {
            $table->dropColumn(['livraison_disponible', 'retrait_disponible']);
        });
    }
};
