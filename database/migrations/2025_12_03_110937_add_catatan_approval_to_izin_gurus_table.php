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
        Schema::table('izin_gurus', function (Blueprint $table) {
            $table->text('catatan_approval')->nullable()->after('tanggal_approval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izin_gurus', function (Blueprint $table) {
            $table->dropColumn('catatan_approval');
        });
    }
};
