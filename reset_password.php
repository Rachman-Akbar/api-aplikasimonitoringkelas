<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$affected = DB::table('users')
    ->where('email', 'siswa@sekolah.com')
    ->update(['password' => Hash::make('password')]);

echo "Password updated for siswa@sekolah.com" . PHP_EOL;
echo "Affected rows: " . $affected . PHP_EOL;
