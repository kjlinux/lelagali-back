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
            $table->foreignId('restaurateur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('moyen_paiement_id')->constrained()->onDelete('cascade');
            $table->string('numero_compte')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
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
