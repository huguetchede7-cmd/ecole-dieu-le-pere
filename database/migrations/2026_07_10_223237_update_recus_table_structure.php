<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recus', function (Blueprint $table) {
            $table->foreignId('inscription_id')->nullable()->after('id')->constrained('inscriptions')->nullOnDelete();
            $table->dropForeign(['paiement_id']);
            $table->dropColumn('paiement_id');
        });
    }

    public function down(): void
    {
        Schema::table('recus', function (Blueprint $table) {
            $table->dropForeign(['inscription_id']);
            $table->dropColumn('inscription_id');
            $table->foreignId('paiement_id')->nullable()->constrained('paiements')->nullOnDelete();
        });
    }
};