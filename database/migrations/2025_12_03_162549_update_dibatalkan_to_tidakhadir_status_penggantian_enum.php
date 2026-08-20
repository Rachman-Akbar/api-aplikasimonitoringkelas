<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('guru_pengganties')
            ->where('status_penggantian', 'dibatalkan')
            ->update(['status_penggantian' => 'tidak_hadir']);
    }

    public function down(): void
    {
        DB::table('guru_pengganties')
            ->where('status_penggantian', 'tidak_hadir')
            ->update(['status_penggantian' => 'dibatalkan']);
    }
};
