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
    Schema::table('epreuves', function (Blueprint $table) {
        $table->string('fichier_final')->nullable()->after('nom_original');
        $table->string('nom_final')->nullable()->after('fichier_final');
        $table->foreignId('final_par')->nullable()->after('nom_final')->constrained('utilisateurs')->nullOnDelete();
        $table->timestamp('final_le')->nullable()->after('final_par');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('epreuves', function (Blueprint $table) {
            //
        });
    }
};
