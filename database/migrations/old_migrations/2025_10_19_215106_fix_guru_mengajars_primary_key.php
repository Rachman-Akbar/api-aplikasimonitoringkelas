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
        // Add primary key and auto increment to id column
        DB::statement('ALTER TABLE `guru_mengajars` ADD PRIMARY KEY (`id`)');
        DB::statement('ALTER TABLE `guru_mengajars` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `guru_mengajars` MODIFY `id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `guru_mengajars` DROP PRIMARY KEY');
    }
};
