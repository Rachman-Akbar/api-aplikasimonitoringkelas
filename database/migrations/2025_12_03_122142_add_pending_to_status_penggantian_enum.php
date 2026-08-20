<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru_pengganties', function (Blueprint $table) {
            $table->string('status_penggantian', 30)->default('pending')->change();
        });

        DB::table('guru_pengganties')
            ->where('status_penggantian', 'dijadwalkan')
            ->update(['status_penggantian' => 'pending']);
    }

    public function down(): void
    {
        DB::table('guru_pengganties')
            ->where('status_penggantian', 'pending')
            ->update(['status_penggantian' => 'dijadwalkan']);

        Schema::table('guru_pengganties', function (Blueprint $table) {
            $table->string('status_penggantian', 30)->default('dijadwalkan')->change();
        });
    }
};
