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
            $table->unsignedBigInteger('disetujui_oleh')->nullable()->after('dibuat_oleh');
            $table->foreign('disetujui_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_pengganties', function (Blueprint $table) {
            $table->dropForeign(['disetujui_oleh']);
            $table->dropColumn('disetujui_oleh');
        });
    }
};
