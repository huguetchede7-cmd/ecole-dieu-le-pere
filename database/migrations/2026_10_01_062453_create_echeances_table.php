<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('echeances', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->string('annee_scolaire');
            $table->date('date_limite');
            $table->decimal('montant', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('echeances');
    }
};