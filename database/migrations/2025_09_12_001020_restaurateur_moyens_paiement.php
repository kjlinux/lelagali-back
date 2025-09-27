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
        Schema::create('restaurateur_moyens_paiement', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurateur_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('moyen_paiement_id')->constrained()->onDelete('cascade');
            $table->string('numero_compte')->nullable();
            $table->string('nom_titulaire')->nullable()->after('numero_compte');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurateur_moyens_paiement');
    }
};
