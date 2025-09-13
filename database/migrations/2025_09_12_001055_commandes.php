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
        Schema::create('commandes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_commande')->unique();
            $table->foreignUuid('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('restaurateur_id')->constrained('users')->onDelete('cascade');
            $table->integer('total_plats');
            $table->integer('frais_livraison')->default(0);
            $table->integer('total_general');
            // $table->enum('type_commande', ['precommande', 'directe'])->default('directe');
            $table->enum('type_service', ['livraison', 'retrait'])->default('retrait');
            $table->text('adresse_livraison')->nullable();
            $table->foreignUuid('quartier_livraison_id')->nullable()->constrained('quartiers')->onDelete('set null');
            $table->foreignUuid('moyen_paiement_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['en_attente', 'confirmee', 'prete', 'en_livraison', 'recuperee'])->default('en_attente');
            $table->boolean('status_paiement')->default(false);
            // $table->timestamp('date_commande');
            // $table->timestamp('heure_souhaitee')->nullable();
            // $table->text('notes_client')->nullable();
            // $table->text('notes_restaurateur')->nullable();
            $table->integer('temps_preparation_estime')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
