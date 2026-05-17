<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

if (!is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0777, true);
}

require __DIR__ . '/../public/index.php';
