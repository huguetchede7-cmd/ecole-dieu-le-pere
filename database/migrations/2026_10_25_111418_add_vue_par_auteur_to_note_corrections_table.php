<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
        public function up(): void
    {
        Schema::table('note_corrections', function (Blueprint $table) {
            $table->timestamp('vue_par_auteur_le')->nullable()->after('motif_refus');
        });

        // Les corrections déjà traitées jusqu'ici sont considérées comme déjà consultées
        DB::table('note_corrections')->update(['vue_par_auteur_le' => now()]);
    }

    public function down(): void
    {
        Schema::table('note_corrections', function (Blueprint $table) {
            $table->dropColumn('vue_par_auteur_le');
        });
    }
};
