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
    Schema::create('epreuves', function (Blueprint $table) {
        $table->id();
        $table->foreignId('examen_id')->constrained('examens')->cascadeOnDelete();
        $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
        $table->foreignId('matiere_id')->constrained('matieres')->cascadeOnDelete();
        $table->string('fichier');
        $table->string('nom_original');
        $table->foreignId('depose_par')->nullable()->constrained('utilisateurs')->nullOnDelete();
        $table->boolean('depose_en_retard')->default(false);
        $table->timestamps();

        $table->unique(['examen_id', 'classe_id', 'matiere_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epreuves');
    }
};
