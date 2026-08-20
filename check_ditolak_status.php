<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking guru_pengganties status distribution..." . PHP_EOL . PHP_EOL;

$statuses = \DB::table('guru_pengganties')
    ->select('status_penggantian', \DB::raw('count(*) as count'))
    ->groupBy('status_penggantian')
    ->orderBy('count', 'desc')
    ->get();

echo "Status Distribution:" . PHP_EOL;
foreach ($statuses as $status) {
    echo "  {$status->status_penggantian}: {$status->count} records" . PHP_EOL;
}

$total = \DB::table('guru_pengganties')->count();
echo "\nTotal: {$total} records" . PHP_EOL;

// Check if there are any 'ditolak' records
$ditolakCount = \DB::table('guru_pengganties')->where('status_penggantian', 'ditolak')->count();
echo "\n" . ($ditolakCount > 0 ? "✓" : "✗") . " Records with 'ditolak' status: {$ditolakCount}" . PHP_EOL;

// Show sample ditolak record if exists
if ($ditolakCount > 0) {
    $sample = \DB::table('guru_pengganties')->where('status_penggantian', 'ditolak')->first();
    echo "\nSample 'ditolak' record:" . PHP_EOL;
    echo "  ID: {$sample->id}" . PHP_EOL;
    echo "  Tanggal: {$sample->tanggal}" . PHP_EOL;
    echo "  Status: {$sample->status_penggantian}" . PHP_EOL;
    echo "  Catatan Approval: {$sample->catatan_approval}" . PHP_EOL;
}
