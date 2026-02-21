<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    $svc = $app->make(App\Services\WablasService::class);
    echo "OK: " . get_class($svc) . PHP_EOL;
} catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage() . PHP_EOL;
} catch (Throwable $t) {
    echo "Error: " . $t->getMessage() . PHP_EOL;
}
