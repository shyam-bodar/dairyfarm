<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "dairyfarm";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// Auto-detect base URL (works in root or subfolder)
$projectRoot = str_replace('\\', '/', dirname(__DIR__));
$docRoot     = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/'));
if ($docRoot !== '' && strpos($projectRoot, $docRoot) === 0) {
    $base = substr($projectRoot, strlen($docRoot));
    $base = ($base === '') ? '/' : $base . '/';
} else {
    $base = '/';
}
define('SITE_URL', $base);
define('CURRENCY', '₹');
