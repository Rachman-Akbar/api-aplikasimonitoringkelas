<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('email', 'siswa@sekolah.com')->first();

echo "=== USER OBJECT DEBUG ===" . PHP_EOL;
echo "ID: " . $user->id . PHP_EOL;
echo "Name: " . $user->name . PHP_EOL;
echo "Email: " . $user->email . PHP_EOL;
echo "Role: " . $user->role . PHP_EOL;
echo "Kelas ID (direct access): " . $user->kelas_id . PHP_EOL;
echo "Guru ID (direct access): " . ($user->guru_id ?? 'null') . PHP_EOL;

echo PHP_EOL . "=== toArray() ===" . PHP_EOL;
print_r($user->toArray());

echo PHP_EOL . "=== toJson() ===" . PHP_EOL;
echo $user->toJson(JSON_PRETTY_PRINT) . PHP_EOL;

echo PHP_EOL . "=== Manual Array ===" . PHP_EOL;
$manual = [
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'role' => $user->role,
    'kelas_id' => $user->kelas_id,
    'guru_id' => $user->guru_id,
];
echo json_encode($manual, JSON_PRETTY_PRINT) . PHP_EOL;
