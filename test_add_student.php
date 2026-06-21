<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Login
$req1 = Illuminate\Http\Request::create(
    '/kh/api/login',
    'POST',
    ['name' => '231011403268', 'password' => '231011403268KK']
);
$res1 = $kernel->handle($req1);

// Add Student
$req2 = Illuminate\Http\Request::create(
    '/kh/student',
    'POST',
    [
        'nim' => '2310000',
        'name' => 'Siswa Baru',
        'role' => 'mahasiswa'
    ]
);
// Simulate AJAX
$req2->headers->set('Accept', 'application/json');
$req2->headers->set('X-Requested-With', 'XMLHttpRequest');

// Share session
$session_cookies = [];
foreach ($res1->headers->getCookies() as $cookie) {
    if ($cookie->getName() === 'XSRF-TOKEN') {
        $req2->headers->set('X-XSRF-TOKEN', urldecode($cookie->getValue()));
    }
    $req2->cookies->set($cookie->getName(), $cookie->getValue());
}

$res2 = $kernel->handle($req2);

echo "Status: " . $res2->getStatusCode() . "\n";
echo "Response: " . $res2->getContent() . "\n";
