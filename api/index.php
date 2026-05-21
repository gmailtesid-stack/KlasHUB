<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Force Laravel to use the writable /tmp directory on Vercel Serverless Functions
$app->useStoragePath('/tmp');

// Matches VIEW_COMPILED_PATH in vercel.json
$dirs = [
    '/tmp/framework/views',
    '/tmp/framework/cache/data',
    '/tmp/framework/sessions',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Ensure the CA bundle is present for SSL database connections
$cacertPath = '/tmp/cacert.pem';
if (!file_exists($cacertPath)) {
    $source = __DIR__ . '/../cacert.pem';
    if (file_exists($source)) {
        copy($source, $cacertPath);
    }
}

$app->handleRequest(Request::capture());
