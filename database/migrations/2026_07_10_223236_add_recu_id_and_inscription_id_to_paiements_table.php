<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('recu_id')->nullable()->after('id')->constrained('recus')->nullOnDelete();
            $table->foreignId('inscription_id')->nullable()->after('recu_id')->constrained('inscriptions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['recu_id']);
            $table->dropForeign(['inscription_id']);
            $table->dropColumn(['recu_id', 'inscription_id']);
        });
    }
};