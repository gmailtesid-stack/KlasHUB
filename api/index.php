<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Report errors for debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Force writable storage in /tmp for Vercel
$app->useStoragePath('/tmp');

// Ensure necessary directories exist in /tmp
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

// CA Bundle for DB
if (!file_exists('/tmp/cacert.pem')) {
    @copy(__DIR__ . '/../cacert.pem', '/tmp/cacert.pem');
}

// Capture and handle the request
$request = Request::capture();

// Force HTTPS if on Vercel
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

$response = $app->handle($request);
$response->send();

// The handle terminates the app automatically in some versions, 
// but we call it explicitly if needed for cleanup.
if (method_exists($app, 'terminate')) {
    $app->terminate();
}
