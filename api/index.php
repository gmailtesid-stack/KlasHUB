<?php
// Report all errors earliest 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // 1. Verify Autoload
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload)) {
        die("Autoload file not found at: " . $autoload . ". Current dir: " . __DIR__ . ". Files here: " . implode(', ', scandir(__DIR__)));
    }
    require $autoload;

    // 2. Verify Bootstrap
    $bootstrap = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($bootstrap)) {
        die("Bootstrap file not found at: " . $bootstrap);
    }
    $app = require_once $bootstrap;

    // 3. Storage & Env Setup
    $app->useStoragePath('/tmp');

    // Create necessary folders
    $dirs = [
        '/tmp/framework/views',
        '/tmp/framework/cache/data',
        '/tmp/framework/sessions',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }

    // 4. SSL / CA handling
    if (!file_exists('/tmp/cacert.pem')) {
        @copy(__DIR__ . '/../cacert.pem', '/tmp/cacert.pem');
    }

    // 5. Handle Request
    $request = \Illuminate\Http\Request::capture();
    $app->handleRequest($request);

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Internal Crash Detected</h1>";
    echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>Loc:</strong> " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit(1);
}
