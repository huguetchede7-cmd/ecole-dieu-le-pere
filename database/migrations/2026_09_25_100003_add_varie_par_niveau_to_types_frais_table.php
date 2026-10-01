<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('types_frais', function (Blueprint $table) {
            $table->boolean('varie_par_niveau')->default(false)->after('libelle');
            $table->decimal('montant', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('types_frais', function (Blueprint $table) {
            $table->dropColumn('varie_par_niveau');
        });
    }
};