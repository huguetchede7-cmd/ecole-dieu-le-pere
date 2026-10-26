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
        Schema::create('examens', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('type', 30)->default('Composition');
            $table->string('periode', 20);
            $table->string('annee_scolaire', 9);
            $table->date('date_limite_depot');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->date('date_limite_notes');
            $table->text('consignes')->nullable();
            $table->foreignId('cree_par')->nullable()->constrained('utilisateurs')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examens');
    }
};
