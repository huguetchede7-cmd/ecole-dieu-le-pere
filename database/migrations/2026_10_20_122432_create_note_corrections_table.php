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
        Schema::create('note_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained('notes')->cascadeOnDelete();
            $table->decimal('ancienne_note', 6, 2);
            $table->decimal('ancienne_note_max', 6, 2);
            $table->decimal('nouvelle_note', 6, 2);
            $table->decimal('nouvelle_note_max', 6, 2);
            $table->string('motif')->nullable();
            $table->foreignId('modifie_par')->nullable()->constrained('utilisateurs')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('note_corrections');
    }
};
