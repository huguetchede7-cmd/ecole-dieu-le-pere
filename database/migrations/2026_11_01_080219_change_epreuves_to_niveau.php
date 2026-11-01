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
    // Les épreuves déposées jusqu'ici sont des essais : on les efface pour repartir proprement
    \Illuminate\Support\Facades\DB::table('epreuves')->delete();
    \Illuminate\Support\Facades\Storage::disk('local')->deleteDirectory('epreuves');

    Schema::table('epreuves', function (Blueprint $table) {
        $table->string('niveau', 30)->after('examen_id');
        $table->unique(['examen_id', 'niveau', 'matiere_id']);
    });

    Schema::table('epreuves', function (Blueprint $table) {
        $table->dropUnique(['examen_id', 'classe_id', 'matiere_id']);
        $table->dropForeign(['classe_id']);
        $table->dropColumn('classe_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('niveau', function (Blueprint $table) {
            //
        });
    }
};
