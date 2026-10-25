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
        Schema::table('note_corrections', function (Blueprint $table) {
            // Les corrections déjà faites sont considérées comme confirmées
            $table->string('statut', 20)->default('validee')->after('motif');
            $table->foreignId('validateur_id')->nullable()->after('statut')->constrained('utilisateurs')->nullOnDelete();
            $table->timestamp('decide_le')->nullable()->after('validateur_id');
            $table->string('motif_refus')->nullable()->after('decide_le');
        });
    }

    public function down(): void
    {
        Schema::table('note_corrections', function (Blueprint $table) {
            $table->dropConstrainedForeignId('validateur_id');
            $table->dropColumn(['statut', 'decide_le', 'motif_refus']);
        });
    }
};
