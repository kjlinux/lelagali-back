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
        Schema::create('tarif_livraisons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurateur_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('quartier_id')->constrained()->onDelete('cascade');
            $table->integer('prix');
            $table->timestamps();
            $table->unique(['restaurateur_id', 'quartier_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_livraisons');
    }
};
