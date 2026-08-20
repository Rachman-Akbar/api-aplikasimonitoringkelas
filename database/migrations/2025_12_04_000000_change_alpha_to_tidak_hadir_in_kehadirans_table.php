<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->string('status', 30)->default('hadir')->change();
        });

        DB::table('kehadirans')
            ->where('status', 'alpha')
            ->update(['status' => 'tidak_hadir']);
    }

    public function down(): void
    {
        DB::table('kehadirans')
            ->where('status', 'tidak_hadir')
            ->update(['status' => 'alpha']);
    }
};
