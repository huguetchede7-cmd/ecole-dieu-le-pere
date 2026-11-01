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
    Schema::table('examens', function (Blueprint $table) {
        $table->date('date_ouverture_impression')->nullable()->after('date_limite_notes');
        $table->foreignId('ouverture_anticipee_par')->nullable()->constrained('utilisateurs')->nullOnDelete();
        $table->timestamp('ouverture_anticipee_le')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examens', function (Blueprint $table) {
            //
        });
    }
};
