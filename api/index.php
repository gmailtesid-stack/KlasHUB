<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Laravel 13 Boot Diagnostic</h1>";

try {
    echo "Loading Autoload... ";
    require __DIR__ . '/../vendor/autoload.php';
    echo "OK<br>";

    echo "Loading Bootstrap... ";
    $bootstrap = __DIR__ . '/../bootstrap/app.php';
    if (!file_exists($bootstrap)) {
        die("Bootstrap missing at $bootstrap");
    }
    $app = require_once $bootstrap;
    echo "OK<br>";

    echo "Configuring Storage Path... ";
    $app->useStoragePath('/tmp');
    echo "OK<br>";

    echo "Attempting to create directories in /tmp... ";
    $dirs = ['/tmp/framework/views', '/tmp/framework/cache/data', '/tmp/framework/sessions'];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
    }
    echo "OK<br>";

    echo "Attempting to boot Application... ";
    $app->make(\Illuminate\Contracts\Console\Kernel::class); // Just to see if it boots
    echo "OK (Kernel Loaded)<br>";

    echo "Attempting to Capture Request... ";
    $request = \Illuminate\Http\Request::capture();
    echo "OK<br>";

    echo "Attempting to Handle Request... ";
    $response = $app->handle($request);
    echo "OK (Response Ready)<br>";

    $response->send();
    echo "<br>Response Sent Successfully.";

} catch (\Throwable $e) {
    echo "<hr><h2 style='color:red'>CRASH DETECTED</h2>";
    echo "<strong>Message:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $e->getFile() . ":" . $e->getLine() . "<br>";
    echo "<strong>Type:</strong> " . get_class($e) . "<br>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
