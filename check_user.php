<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = DB::table('users')
    ->where('email', 'siswa@sekolah.com')
    ->first(['id', 'name', 'email', 'role', 'kelas_id', 'guru_id']);

echo json_encode($user, JSON_PRETTY_PRINT) . PHP_EOL;
