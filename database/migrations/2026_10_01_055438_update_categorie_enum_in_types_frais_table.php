<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE types_frais MODIFY categorie ENUM('inscription', 'reinscription', 'scolarite', 'autre') DEFAULT 'autre'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE types_frais MODIFY categorie ENUM('inscription', 'reinscription', 'autre') DEFAULT 'autre'");
    }
};