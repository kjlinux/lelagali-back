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
        Schema::create('plats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom');
            $table->text('description');
            $table->integer('prix');
            $table->integer('quantite_disponible')->default(0);
            $table->integer('quantite_vendue')->default(0);
            $table->string('image')->nullable();
            $table->foreignUuid('restaurateur_id')->constrained('users')->onDelete('cascade');
            $table->date('date_disponibilite'); // Uniquement lendemain
            $table->boolean('is_approved')->default(true);
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->integer('temps_preparation')->nullable(); // minutes
            $table->timestamps();
            $table->index(['date_disponibilite', 'is_approved']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plats');
    }
};
