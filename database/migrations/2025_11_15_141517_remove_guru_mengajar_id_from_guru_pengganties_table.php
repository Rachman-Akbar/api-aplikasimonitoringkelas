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
        Schema::table('guru_pengganties', function (Blueprint $table) {
            if (Schema::hasColumn('guru_pengganties', 'guru_mengajar_id')) {
                $table->dropForeign(['guru_mengajar_id']);
                $table->dropColumn('guru_mengajar_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_pengganties', function (Blueprint $table) {
            // Tidak perlu restore karena kolom ini sudah tidak digunakan
        });
    }
};
