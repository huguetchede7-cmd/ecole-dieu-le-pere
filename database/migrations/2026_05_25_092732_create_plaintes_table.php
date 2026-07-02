<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plaintes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
            $table->foreignId('secretaire_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->text('description');
            $table->enum('statut', ['en_cours', 'resolue', 'rejetee'])->default('en_cours');
            $table->text('reponse')->nullable();
            $table->date('date_plainte');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plaintes');
    }
};