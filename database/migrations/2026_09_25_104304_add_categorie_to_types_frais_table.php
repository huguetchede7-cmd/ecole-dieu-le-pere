<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('types_frais', function (Blueprint $table) {
            $table->enum('categorie', ['inscription', 'reinscription', 'autre'])->default('autre')->after('libelle');
        });
    }

    public function down(): void
    {
        Schema::table('types_frais', function (Blueprint $table) {
            $table->dropColumn('categorie');
        });
    }
};