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
            $table->dropForeign(['dibuat_oleh']); // Drop the foreign key constraint first
            $table->dropColumn('dibuat_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_pengganties', function (Blueprint $table) {
            $table->unsignedBigInteger('dibuat_oleh')->nullable()->after('keterangan');
            $table->foreign('dibuat_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }
};
