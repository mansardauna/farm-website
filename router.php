<?php
// PHP Built-in Server Router Script for Ankabit Farm
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Determine file path
$filePath = __DIR__ . $uri;

if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    // Return false to let PHP serve the static asset directly with correct MIME type
    return false;
}

// Check inside public/ directory if asset is requested
$publicPath = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    $mime = mime_content_type($publicPath);
    if (str_ends_with($uri, '.css')) $mime = 'text/css';
    if (str_ends_with($uri, '.js')) $mime = 'application/javascript';
    header("Content-Type: $mime");
    readfile($publicPath);
    exit;
}

// Clean URL Routing to PHP files
if ($uri === '/' || $uri === '/index' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    exit;
}

if ($uri === '/order' || $uri === '/order.php') {
    require __DIR__ . '/order.php';
    exit;
}

if ($uri === '/privacy' || $uri === '/privacy.php') {
    require __DIR__ . '/privacy.php';
    exit;
}

if ($uri === '/terms' || $uri === '/terms.php') {
    require __DIR__ . '/terms.php';
    exit;
}

if ($uri === '/api/captcha' || $uri === '/captcha.php') {
    require __DIR__ . '/captcha.php';
    exit;
}

if ($uri === '/api/leads/step1' || $uri === '/api/leads/step2' || $uri === '/process-lead.php') {
    require __DIR__ . '/process-lead.php';
    exit;
}

// Fallback to index.php
require __DIR__ . '/index.php';
?>
