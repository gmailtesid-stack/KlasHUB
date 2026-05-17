<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Force Laravel to use the writable /tmp directory on Vercel Serverless Functions
$app->useStoragePath('/tmp');

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

// Generate the specific Let's Encrypt root certificate required by TiDB Cloud
// This avoids Vercel-PHP bundler exclusions and Amazon Linux system CA path issues
if (!file_exists('/tmp/cacert.pem')) {
    $caBundle = @file_get_contents('https://curl.se/ca/cacert.pem');
    if ($caBundle) {
        file_put_contents('/tmp/cacert.pem', $caBundle);
    }
}

$app->handleRequest(Request::capture());
