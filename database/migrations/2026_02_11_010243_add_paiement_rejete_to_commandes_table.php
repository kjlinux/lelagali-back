<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->boolean('paiement_rejete')->default(false)->after('status_paiement');
            $table->text('raison_rejet_paiement')->nullable()->after('paiement_rejete');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['paiement_rejete', 'raison_rejet_paiement']);
        });
    }
};
