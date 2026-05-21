<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Vercel PHP Diagnostic</h1>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Dir: " . __DIR__ . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

$autoload = __DIR__ . '/../vendor/autoload.php';
echo "Checking Autoload at $autoload: " . (file_exists($autoload) ? "FOUND" : "NOT FOUND") . "<br>";

$cacert = __DIR__ . '/../cacert.pem';
echo "Checking CA Bundle at $cacert: " . (file_exists($cacert) ? "FOUND" : "NOT FOUND") . "<br>";

if (file_exists($autoload)) {
    echo "Attempting to require autoload...<br>";
    require $autoload;
    echo "Autoload SUCCESS<br>";
}

echo "Diagnostic Finished.";
