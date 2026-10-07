<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

// --- TAMBAHKAN KODE INI SEMENTARA ---
echo "<b>Lokasi folder saat ini:</b> " . __DIR__ . "<br>";
echo "<b>Apakah file .env ada?</b> " . (file_exists(__DIR__.'/.env') ? "ADA" : "TIDAK ADA") . "<br>";
$env_content = @file_get_contents(__DIR__.'/.env');
echo "<b>Bisa dibaca PHP?</b> " . ($env_content !== false ? "BISA" : "TIDAK (cek file permissions)") . "<br>";
if ($env_content !== false) {
    echo "<b>Isi APP_KEY di file:</b> " . (preg_match('/APP_KEY=(.*)/', $env_content, $matches) ? $matches[1] : 'TIDAK DITEMUKAN') . "<br>";
}
exit;
// ------------------------------------

$app->handleRequest(Request::capture());
