<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('frais_niveaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_frais_id')->constrained('types_frais')->onDelete('cascade');
            $table->string('niveau');
            $table->decimal('montant', 10, 2);
            $table->timestamps();

            $table->unique(['type_frais_id', 'niveau']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('frais_niveaux');
    }
};