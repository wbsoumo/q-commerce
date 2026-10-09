<?php

// Enable error reporting to display exact error trace on screen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Laravel - A PHP Framework For Web Artisans
 * Root Forwarder for cPanel hosting environments
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri) && !is_dir(__DIR__.'/public'.$uri)) {
    $filePath = __DIR__.'/public'.$uri;
    $mimeType = str_ends_with($filePath, '.apk') ? 'application/vnd.android.package-archive' : (mime_content_type($filePath) ?: 'application/octet-stream');
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filePath));
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    readfile($filePath);
    exit;
}

require_once __DIR__.'/public/index.php';
