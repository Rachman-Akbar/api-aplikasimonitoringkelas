<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the status column to support new enum values
        DB::statement("ALTER TABLE guru_mengajars MODIFY COLUMN status ENUM('aktif', 'nonaktif', 'masuk', 'tidak masuk', 'izin', 'sakit') NOT NULL DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE guru_mengajars MODIFY COLUMN status ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif'");
    }
};
