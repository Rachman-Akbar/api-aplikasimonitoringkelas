<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

$request = Illuminate\Http\Request::create('/api/auth/login', 'POST', [
    'email' => 'siswa@sekolah.com',
    'password' => 'password'
]);

$response = $kernel->handle($request);

$responseBody = json_decode($response->getContent(), true);

echo "Status Code: " . $response->getStatusCode() . PHP_EOL;
echo "Response Body:" . PHP_EOL;
echo json_encode($responseBody, JSON_PRETTY_PRINT) . PHP_EOL;

echo PHP_EOL . "=== CHECKING kelas_id in response ===" . PHP_EOL;
if (isset($responseBody['data']['user']['kelas_id'])) {
    echo "✓ kelas_id is present: " . $responseBody['data']['user']['kelas_id'] . PHP_EOL;
} else {
    echo "✗ kelas_id is MISSING!" . PHP_EOL;
}

if (isset($responseBody['data']['user']['guru_id'])) {
    echo "✓ guru_id is present: " . ($responseBody['data']['user']['guru_id'] ?? 'null') . PHP_EOL;
} else {
    echo "✗ guru_id is MISSING!" . PHP_EOL;
}

$kernel->terminate($request, $response);
